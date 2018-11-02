<?php

namespace AppBundle\Dto;

/**
 * Class CityDto
 *
 * @package AppBundle\Dto
 */
class CityDto
{
    /**
     * @var int
     */
    public $cityId;

    /**
     * @var string
     */
    public $cityTitle;

    /**
     * CityDto constructor.
     *
     * @param int    $cityId
     * @param string $cityTitle
     */
    public function __construct(int $cityId, string $cityTitle)
    {
        $this->cityId = $cityId;
        $this->cityTitle = $cityTitle;
    }
}
