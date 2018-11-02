<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\Amenity;
use AppBundle\Entity\Room;
use Common\Doctrine\Extensions\IsNull;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetHotelAmenitiesPrettyAction
 *
 * @package AppBundle\Action\Admin
 */
class GetRoomAmenitiesPrettyAction
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * GetHotelAmenitiesPrettyAction constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $roomId
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @Route(
     *     name="get_room_amenities_pretty",
     *     path="/api/v3/get-room-amenities-pretty/{roomId}",
     *     requirements={
     *         "hotelId"= "\d+",
     *     },
     * )
     *
     * @Method({"GET"})
     */
    public function __invoke(int $roomId)
    {
        /** @var Room $roomRef */
        $roomRef = $this->em->getReference(Room::class, $roomId);

        $amenityRepository = $this->em->getRepository(Amenity::class);

        $query = $amenityRepository->getActiveAmenitiesWithRoomAmenitiesQuery($roomRef, 'a', 'ra');
        $this->em->getConfiguration()->addCustomStringFunction('ISNULL', IsNull::class);
        $query
            ->addOrderBy('ra.isActive', 'desc')
            ->addOrderBy('ISNULL(ra.id)', 'desc')
            ->addOrderBy('a.id', 'asc');

        return new JsonResponse($query->getQuery()->getArrayResult());
    }
}
