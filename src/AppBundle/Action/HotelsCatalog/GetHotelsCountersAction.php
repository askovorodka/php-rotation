<?php

namespace AppBundle\Action\HotelsCatalog;

use AppBundle\Services\HotelsCatalogV2\HotelsCatalogServiceInterface;
use AppBundle\Dto\HotelsSearch\SearchDtoAssembler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Action для получения счетчиков отелей для построения фильтра
 *
 * @package AppBundle\Action\HotelsCatalog
 */
class GetHotelsCountersAction
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var HotelsCatalogServiceInterface
     */
    protected $hotelsCatalogService;

    /**
     * GetHotelsCountersAction constructor.
     *
     * @param RequestStack                  $requestStack
     * @param HotelsCatalogServiceInterface $hotelsCatalogService
     */
    public function __construct(
        RequestStack $requestStack,
        HotelsCatalogServiceInterface $hotelsCatalogService
    ) {
        $this->requestStack = $requestStack;
        $this->hotelsCatalogService = $hotelsCatalogService;
    }

    /**
     * @Route(
     *     name="hotels_catalog.get_hotels_counters",
     *     path="/api/v3/hotels-catalog/hotels-counters",
     * )
     *
     * @Method({"GET"})
     *
     * @throws \Exception
     */
    public function __invoke()
    {
        $searchDtoAssembler = new SearchDtoAssembler($this->requestStack->getCurrentRequest());

        return new JsonResponse(
            $this->hotelsCatalogService->getCounters($searchDtoAssembler->assemble())
        );
    }
}
