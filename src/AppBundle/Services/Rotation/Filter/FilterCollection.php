<?php

namespace AppBundle\Services\Rotation\Filter;

use AppBundle\Services\Rotation\Filter\Dto\FilterDto;

/**
 * Class FilterCollection
 *
 * @package AppBundle\Services\Rotation\Filter
 */
class FilterCollection implements \Iterator
{
    /**
     * @var FilterDto[]
     */
    private $filters = [];

    /**
     * @param FilterDto $filterDto
     * @return FilterCollection
     */
    public function add(FilterDto $filterDto): FilterCollection
    {
        $this->filters[$filterDto->field] = $filterDto;
        return $this;
    }

    /**
     * @inheritdoc
     * @return FilterDto
     */
    public function current(): FilterDto
    {
        return current($this->filters);
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        next($this->filters);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->filters);
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return key($this->filters) !== null;
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        reset($this->filters);
    }
}
