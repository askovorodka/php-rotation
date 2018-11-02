<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\Hotel;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HotelsAdministrativeAreasAction
{

    /** @var ManagerRegistry $managerRegistry */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route(
     *     name="get_hotels_adminnistrative_areas",
     *     path="/api/v3/hotels-administrative-areas"
     * )
     *
     * @Method({"GET"})
     */
    public function __invoke()
    {
        $hotelRepository = $this->managerRegistry->getRepository(Hotel::class);
        $administrativeAreas = $hotelRepository->getAdministrativeAreasQuery()->getQuery()->getArrayResult();

        return new JsonResponse(array_column($administrativeAreas, 'administrativeArea'));
    }

}
