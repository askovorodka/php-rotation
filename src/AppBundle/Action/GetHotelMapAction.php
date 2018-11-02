<?php

namespace AppBundle\Action;

use AppBundle\Services\HotelMap\HotelMapServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GetHotelMapAction
 *
 * @package AppBundle\Action
 */
class GetHotelMapAction
{
    /**
     * @var HotelMapServiceInterface
     */
    private $hotelMapService;

    /**
     * GetHotelMapAction constructor.
     *
     * @param HotelMapServiceInterface $hotelMapService
     */
    public function __construct(HotelMapServiceInterface $hotelMapService)
    {
        $this->hotelMapService = $hotelMapService;
    }

    /**
     * @Route(
     *     name="hotel-map",
     *     path="/api/v3/hotel-map",
     * )
     *
     * @Method({"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $area = $request->get('area');

        $hotelMap = $this->hotelMapService->getHotelMap($area);

        return new JsonResponse($hotelMap);
    }
}
