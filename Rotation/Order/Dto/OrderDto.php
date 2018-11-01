<?php

namespace AppBundle\Services\Rotation\Order\Dto;

/**
 * Class OrderDto
 *
 * @package AppBundle\Services\Rotation\Order\Dto
 */
class OrderDto
{
    /**
     * @var int
     */
    public $num;

    /**
     * @var string
     */
    public $field;

    /**
     * OrderDto constructor.
     *
     * @param int $num
     * @param int $field
     */
    public function __construct(int $num, int $field)
    {
        $this->num = $num;
        $this->field = $field;
    }

}
