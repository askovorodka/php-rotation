<?php

namespace AppBundle\Services\Geocoder\Dto;

class AddressDto
{
    /**
     * @var string|null
     */
    public $country;

    /**
     * @var string|null
     */
    public $administrativeArea;

    /**
     * @var string|null
     */
    public $subAdministrativeArea;

    /**
     * @var string|null
     */
    public $locality;

    /**
     * @var string|null
     */
    public $thoroughfare;

    /**
     * @var string|null
     */
    public $premise;

    /**
     * @var string|null
     */
    public $postalCode;

    /**
     * @var string|null
     */
    public $coordinates;

    /**
     * @var string|null
     */
    public $latitude;

    /**
     * @var string|null
     */
    public $longitude;

    /**
     * @var string|null
     */
    public $countryCode;

    /**
     * @var int|null
     */
    public $cityId;

    /**
     * @var int|null
     */
    public $crmCityId;

    /**
     * @var string|null
     */
    public $crmCityName;
}
