<?php

namespace AppBundle\Services\Geocoder\ValueObjects;

class CoordinatesPoint
{
    /**
     * @var float Широта
     */
    private $latitude;

    /**
     * @var float Долгота
     */
    private $longitude;

    /**
     * CoordinatesPoint constructor.
     *
     * @param $latitude
     * @param $longitude
     */
    public function __construct($latitude, $longitude)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            throw new \InvalidArgumentException('Невалидные значения широты и/или долготы');
        }

        $this->latitude = (float)$latitude;
        $this->longitude = (float)$longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }
}
