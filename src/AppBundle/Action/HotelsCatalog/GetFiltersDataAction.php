<?php

namespace AppBundle\Action\HotelsCatalog;

use AppBundle\Services\HotelsCatalogV2\HotelsCatalogServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Action для получения данных для построения фильтра
 *
 * @package AppBundle\Action\HotelsCatalog
 */
class GetFiltersDataAction
{
    /**
     * @var HotelsCatalogServiceInterface
     */
    private $hotelsCatalogService;

    /**
     * GetFiltersDataAction constructor.
     *
     * @param HotelsCatalogServiceInterface $hotelsCatalogService
     */
    public function __construct(HotelsCatalogServiceInterface $hotelsCatalogService)
    {
        $this->hotelsCatalogService = $hotelsCatalogService;
    }

    /**
     * @Route(
     *     name="hotels_catalog.get_filters_data",
     *     path="/api/v3/hotels-catalog/filters-data",
     * )
     *
     * @Method({"GET"})
     */
    public function __invoke()
    {
        return new JsonResponse(
            $this->hotelsCatalogService->getFiltersData()
        );
    }
}
