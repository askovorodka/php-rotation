<?php

namespace AppBundle\Services\Rotation\Pin\Dto;

/**
 * Class PinDto
 *
 * @package AppBundle\Services\Rotation\Pin\Dto
 */
class PinDto
{
    /**
     * @var string
     */
    public $category;

    /**
     * @var int
     */
    public $position;

    /**
     * @var int|null
     */
    public $pinnedHotelId;

    /**
     * @var int|null
     */
    public $pinnedPresetId;

    /**
     * PinDto constructor.
     *
     * @param int    $position
     * @param string $category
     */
    public function __construct(string $category, int $position)
    {
        $this->category = $category;
        $this->position = $position;
    }

}
