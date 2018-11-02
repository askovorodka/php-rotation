<?php

namespace AppBundle\DataProvider\HotelView;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\DataPreparer\ActiveHotelPreparer;
use AppBundle\Entity\Hotel;
use AppBundle\Services\HotelsSearch\Exceptions\HotelNotFoundException;
use AppBundle\Services\HotelsSearch\HotelsSearchServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class HotelViewMobileItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RepositoryFactory
     */
    private $hotelRepository;

    /**
     * @var HotelsSearchServiceInterface $hotelSearchService
     */
    private $hotelSearchService;

    /**
     * @var ActiveHotelPreparer
     */
    private $hotelManipulator;

    /**
     * HotelViewMobileItemDataProvider constructor.
     *
     * @param EntityManagerInterface       $em
     * @param HotelsSearchServiceInterface $hotelsSearchService
     * @param ActiveHotelPreparer          $hotelManipulator
     */
    public function __construct(
        EntityManagerInterface $em,
        HotelsSearchServiceInterface $hotelsSearchService,
        ActiveHotelPreparer $hotelManipulator
    ) {
        $this->em = $em;
        $this->hotelRepository = $this->em->getRepository(Hotel::class);
        $this->hotelSearchService = $hotelsSearchService;
        $this->hotelManipulator = $hotelManipulator;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Hotel::class && $operationName == "get_hotel_view_mobile";
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Hotel
    {
        $sysname = $id;

        try {
            $foundHotel = $this->hotelSearchService->findBySysname($sysname);
        } catch (HotelNotFoundException $e) {
            throw new NotFoundHttpException(sprintf('Отель %s не найден', $sysname));
        }

        /** @var Hotel $hotel */
        $hotel = $this->hotelRepository->find($foundHotel->getHotelId());
        if (!$hotel) {
            throw new NotFoundHttpException(sprintf('Отель %s не найден', $sysname));
        }

        $foundHotel->fillHotelMinPrice($hotel);
        $this->hotelManipulator->prepare($hotel);

        return $hotel;
    }
}
