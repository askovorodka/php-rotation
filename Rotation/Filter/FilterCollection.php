<?php

namespace AppBundle\Services\Rotation\Filter;

use AppBundle\Services\Rotation\Filter\Dto\FilterDto;

class FilterCollection
{
    /**
     * @var FilterDto[]
     */
    private $filters = [];

    /**
     * @param FilterDto $filterDto
     * @return FilterCollection
     */
    public function addFilter(FilterDto $filterDto): FilterCollection
    {
        $this->filters[$filterDto->field] = $filterDto;
        return $this;
    }

}
