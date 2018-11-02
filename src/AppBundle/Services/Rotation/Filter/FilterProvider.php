<?php

namespace AppBundle\Services\Rotation\Filter;

use AppBundle\Entity\Preset;
use AppBundle\Services\Rotation\Filter\Dto\FilterDto;

/**
 * Class FilterProvider
 *
 * @package AppBundle\Services\Rotation\Filter
 */
class FilterProvider implements FilterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFilters(Preset $preset): FilterCollection
    {
        $filterCollection = new FilterCollection();

        $presetParams = $preset->getParams();

        $rawFilters = $presetParams['filters'] ?? [];
        foreach ($rawFilters as $field => $value) {
            $filter = new FilterDto($field, $value);
            $filterCollection->add($filter);
        }

        return $filterCollection;
    }
}
