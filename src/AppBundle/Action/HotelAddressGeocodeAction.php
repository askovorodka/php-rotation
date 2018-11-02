<?php

namespace AppBundle\Action;

use AppBundle\Services\Geocoder\GeocoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Геокодинг адреса
 *
 * @package AppBundle\Action
 */
class HotelAddressGeocodeAction
{
    /**
     * @var GeocoderInterface
     */
    private $geocoder;

    /**
     * HotelAddressGeocodeAction constructor.
     *
     * @param GeocoderInterface $geocoder
     */
    public function __construct(GeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * @Route(
     *     name="hotels_address_geocode",
     *     path="/api/v3/hotels/address/geocode",
     * )
     * @Method({"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $searchStr = $request->get('address');
        $addressDto = $this->geocoder->findAddress($searchStr);

        return new JsonResponse($addressDto);
    }
}
