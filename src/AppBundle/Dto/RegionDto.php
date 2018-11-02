<?php

namespace AppBundle\Dto;

/**
 * Class RegionDto
 *
 * @package AppBundle\Dto
 */
class RegionDto
{
    /**
     * @var int
     */
    public $regionId;

    /**
     * @var string
     */
    public $regionTitle;

    /**
     * RegionDto constructor.
     *
     * @param int    $regionId
     * @param string $regionTitle
     */
    public function __construct(int $regionId, string $regionTitle)
    {
        $this->regionId = $regionId;
        $this->regionTitle = $regionTitle;
    }

}
