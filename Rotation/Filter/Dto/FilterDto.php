<?php

namespace AppBundle\Services\Rotation\Filter\Dto;

/**
 * Class FilterDto
 *
 * @package AppBundle\Services\Rotation\Filter\Dto
 */
class FilterDto
{
    /**
     * @var string
     */
    public $field;

    /**
     * @var mixed
     */
    public $value;

    /**
     * FilterDto constructor.
     *
     * @param string $field
     * @param        $value
     */
    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

}
