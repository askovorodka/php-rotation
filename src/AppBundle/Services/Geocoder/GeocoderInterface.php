<?php

namespace AppBundle\Services\Geocoder;

use AppBundle\Services\Geocoder\Dto\AddressDto;
use AppBundle\Services\Geocoder\Dto\MetroDto;
use AppBundle\Services\Geocoder\Exceptions\MetroNotFoundException;
use AppBundle\Services\Geocoder\ValueObjects\CoordinatesPoint;

interface GeocoderInterface
{
    /**
     * @param string $searchStr
     * @return AddressDto
     */
    public function findAddress(string $searchStr): AddressDto;

    /**
     * @param CoordinatesPoint $point
     * @return MetroDto
     */
    public function findNearestMetro(CoordinatesPoint $point): MetroDto;
}
