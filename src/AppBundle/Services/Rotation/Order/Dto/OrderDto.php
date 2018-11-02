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
     * @var mixed|null
     */
    public $value;

    /**
     * @var string
     */
    public $direction = 'asc';

    /**
     * OrderDto constructor.
     *
     * @param int    $num
     * @param string $field
     */
    public function __construct(int $num, string $field)
    {
        $this->num = $num;
        $this->field = $field;
    }

}
