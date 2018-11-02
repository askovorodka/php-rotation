<?php

namespace AppBundle\Action\Admin;

use AppBundle\Entity\Hotel;
use AppBundle\Repository\HotelRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetActiveHotelsAction
{

    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route(
     *     name="get_active_hotels",
     *     path="/api/v3/get-active-hotels",
     * )
     *
     * @Method({"GET"})
     */
    public function __invoke(Request $request)
    {
        /** @var HotelRepository */
        $hotelRepository = $this->managerRegistry->getRepository(Hotel::class);

        return new JsonResponse($hotelRepository->getActiveHotels($request->get('search', '')));
    }
}
