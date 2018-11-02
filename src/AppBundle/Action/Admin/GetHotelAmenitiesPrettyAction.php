<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\Amenity;
use AppBundle\Entity\Hotel;
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
class GetHotelAmenitiesPrettyAction
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
     * @param int $hotelId
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @Route(
     *     name="get_hotel_amenities_pretty",
     *     path="/api/v3/get-hotel-amenities-pretty/{hotelId}",
     *     requirements={
     *         "hotelId"= "\d+",
     *     },
     * )
     *
     * @Method({"GET"})
     */
    public function __invoke(int $hotelId)
    {
        /** @var Hotel $hotelRef */
        $hotelRef = $this->em->getReference(Hotel::class, $hotelId);

        $amenityRepository = $this->em->getRepository(Amenity::class);

        $query = $amenityRepository->getActiveAmenitiesWithHotelAmenitiesQuery($hotelRef, 'a', 'ha');
        $this->em->getConfiguration()->addCustomStringFunction('ISNULL', IsNull::class);
        $query
            ->addOrderBy('ha.isActive', 'desc')
            ->addOrderBy('ISNULL(ha.id)', 'desc')
            ->addOrderBy('a.id', 'asc');

        return new JsonResponse($query->getQuery()->getArrayResult());
    }
}
