<?php

namespace AppBundle\DataProvider\Admin;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\Entity\Amenity;
use AppBundle\Entity\AmenityCategory;
use AppBundle\Entity\HotelAmenity;
use AppBundle\Entity\RoomAmenity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class AmenityItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var EntityManager $em */
    private $em;

    /** @var null|\Symfony\Component\HttpFoundation\Request */
    private $request;

    public function __construct(EntityManager $entityManager, RequestStack $requestStack)
    {
        $this->em = $entityManager;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Amenity::class && $operationName === 'put';
    }

    /**
     * @param string $resourceClass
     * @param int|string $id
     * @param string|null $operationName
     * @param array $context
     * @return Amenity|null
     * @throws \Throwable
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Amenity
    {

        /** @var Amenity $amenityEntity */
        $amenityEntity = $this->em->getRepository(Amenity::class)->find($id);
        if (!$amenityEntity) {
            return null;
        }

        /** @var array $parametersAsArray */
        $parametersAsArray = json_decode($this->request->getContent(), true);

        $this->em->getConnection()->beginTransaction();
        try {
            if (isset($parametersAsArray['isActive'])) {
                $amenityEntity->setIsActive((bool)$parametersAsArray['isActive']);
            }
            if (isset($parametersAsArray['isPriority'])) {
                $amenityEntity->setIsPriority((bool)$parametersAsArray['isPriority']);
            }
            $this->em->persist($amenityEntity);
            $this->em->flush();

            //если удобство выключили
            if (!$amenityEntity->getIsActive()) {
                //выключаем все связанные отельные удобства
                if ($amenityEntity->getAmenityCategory()->getSystemName() == AmenityCategory::HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME) {
                    $activeHotelAmenities = $amenityEntity->getHotelAmenities()
                        ->filter(function ($hotelAmenity){
                            /** @var HotelAmenity $hotelAmenity */
                            return $hotelAmenity->getIsActive();
                        });
                    foreach ($activeHotelAmenities as $hotelAmenity) {
                        /** @var HotelAmenity $hotelAmenity */
                        $hotelAmenity->setIsActive(false);
                        $this->em->persist($hotelAmenity);
                    }
                    $this->em->flush();
                }
                //выключает все связанные номерные удобства
                if ($amenityEntity->getAmenityCategory()->getSystemName() == AmenityCategory::ROOM_AMENITIES_CATEGORY_SYSTEM_NAME) {
                    $activeRoomAmenities = $amenityEntity->getRoomAmenities()
                        ->filter(function ($roomAmenity){
                            /** @var RoomAmenity $roomAmenity */
                            return $roomAmenity->getIsActive();
                    });

                    foreach ($activeRoomAmenities as $roomAmenity) {
                        /** @var RoomAmenity $roomAmenity */
                        $roomAmenity->setIsActive(false);
                        $this->em->persist($roomAmenity);
                    }
                    $this->em->flush();
                }
            }
            $this->em->commit();

            return $amenityEntity;

        } catch (\Throwable $exception) {
            $this->em->rollback();
            throw $exception;
        }

    }
}
