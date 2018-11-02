<?php

namespace AppBundle\Command;

use AppBundle\Entity\Amenity;
use AppBundle\Entity\AmenityCategory;
use AppBundle\Entity\HotelAmenity;
use AppBundle\Entity\RoomAmenity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeactivateAmenityCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('deactivate-amenity')
            ->setDescription('Команда выключает все выключенные удобства (is_active=false) в hotelAmenities и roomAmenities');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        /** @var EntityRepository $amenityRepository */
        $amenityRepository = $entityManager->getRepository(Amenity::class);
        $amenities = $amenityRepository->findBy(['isActive' => false]);
        foreach ($amenities as $amenity) {
            /** @var Amenity $amenity */
            if ($amenity->getAmenityCategory()->getSystemName() == AmenityCategory::HOTEL_AMENITIES_CATEGORY_SYSTEM_NAME) {
                $activeHotelAmenities = $amenity->getHotelAmenities()
                    ->filter(function ($hotelAmenity){
                        /** @var HotelAmenity $hotelAmenity */
                        return $hotelAmenity->getIsActive();
                    });

                foreach ($activeHotelAmenities as $hotelAmenity) {
                    /** @var HotelAmenity $hotelAmenity */
                    $hotelAmenity->setIsActive(false);
                    $entityManager->persist($hotelAmenity);
                }
                $entityManager->flush();
            }
            if ($amenity->getAmenityCategory()->getSystemName() == AmenityCategory::ROOM_AMENITIES_CATEGORY_SYSTEM_NAME) {
                $activeRoomAmenities = $amenity->getRoomAmenities()
                    ->filter(function ($roomAmenity){
                        /** @var RoomAmenity $roomAmenity */
                        return $roomAmenity->getIsActive();
                    });

                foreach ($activeRoomAmenities as $roomAmenity) {
                    /** @var RoomAmenity $roomAmenity */
                    $roomAmenity->setIsActive(false);
                    $entityManager->persist($roomAmenity);
                }
                $entityManager->flush();
            }
        }
        $output->writeln('end');
    }
}
