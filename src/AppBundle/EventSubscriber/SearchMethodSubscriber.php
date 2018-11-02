<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Dto\HotelsSearch\SearchDtoAssembler;
use AppBundle\Services\HotelsCatalogV2\HotelsCatalogServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SearchMethodSubscriber implements EventSubscriberInterface
{
    const OPERATION_NAME = 'hotels_catalog.search';
    const COUNTERS_ONLY_KEY = 'counters_only';

    /**
     * @var HotelsCatalogServiceInterface
     */
    private $hotelsCatalogService;

    /**
     * SearchMethodListener constructor.
     *
     * @param HotelsCatalogServiceInterface $hotelsCatalogService
     */
    public function __construct(HotelsCatalogServiceInterface $hotelsCatalogService)
    {
        $this->hotelsCatalogService = $hotelsCatalogService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'filterController',
            KernelEvents::RESPONSE => 'modifyResponse'
        ];
    }

    /**
     * Исключает этап поиска отелей путем удаления контроллера,
     * если передан соответствующий параметр в запросе
     *
     * @param FilterControllerEvent $event
     */
    public function filterController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->get('_api_collection_operation_name') !== self::OPERATION_NAME) {
            return;
        }

        $countersOnly = $request->query->get(self::COUNTERS_ONLY_KEY);

        if ($countersOnly) {
            $event->setController(function () {
                return new Response();
            });
        }
    }

    /**
     * Добавляет счетчики по фильтрам к структуре ответа
     *
     * @param FilterResponseEvent $event
     * @throws \Exception
     */
    public function modifyResponse(FilterResponseEvent $event)
    {
        if ($event->getRequest()->attributes->get('_api_collection_operation_name') !== self::OPERATION_NAME) {
            return;
        }

        $searchDtoAssembler = new SearchDtoAssembler($event->getRequest());
        $hotelsCounters = $this->hotelsCatalogService->getCounters($searchDtoAssembler->assemble());

        $response = $event->getResponse();

        $contentData = json_decode($response->getContent(), true);
        $contentData['counters'] = $hotelsCounters->jsonSerialize();

        $response->setContent(json_encode($contentData));
    }
}
