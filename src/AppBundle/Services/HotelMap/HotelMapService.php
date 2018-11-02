<?php

namespace AppBundle\Services\HotelMap;

use AppBundle\Repository\HotelMapRepository;
use AppBundle\Services\PhotoUrlService;

/**
 * Class HotelMapService
 *
 * @package AppBundle\Services\HotelMap
 */
class HotelMapService implements HotelMapServiceInterface
{
    /**
     * @var HotelMapRepository
     */
    private $hotelMapRepository;

    /**
     * @var PhotoUrlService
     */
    private $photoUrlService;

    /**
     * HotelMapService constructor.
     *
     * @param HotelMapRepository $hotelMapRepository
     * @param PhotoUrlService    $photoUrlService
     */
    public function __construct(HotelMapRepository $hotelMapRepository, PhotoUrlService $photoUrlService)
    {
        $this->hotelMapRepository = $hotelMapRepository;
        $this->photoUrlService = $photoUrlService;
    }

    /**
     * @inheritdoc
     */
    public function getHotelMap(string $area = null): HotelMapPointsCollection
    {
        $mapPoints = $this->hotelMapRepository->getHotelMapPoints($area);

        $hotelsIds = array_keys($mapPoints);
        $mapPhotos = $this->hotelMapRepository->getHotelMapPhotos($hotelsIds);

        $hotelMap = new HotelMapPointsCollection();
        foreach ($mapPoints as $mapPoint) {
            $photoUrl = null;

            if (isset($mapPhotos[$mapPoint->hotelId])) {
                $photoDto = $mapPhotos[$mapPoint->hotelId];

                $photoUrl = $this->photoUrlService->getPhotoUrlByMetadata(
                    $photoDto->areaWidth,
                    $photoDto->areaHeight,
                    $photoDto->offsetTop,
                    $photoDto->offsetLeft,
                    $photoDto->photoFile
                );
            }

            $hotelMap->addPointWithPhoto($mapPoint, $photoUrl);
        }

        return $hotelMap;
    }
}
