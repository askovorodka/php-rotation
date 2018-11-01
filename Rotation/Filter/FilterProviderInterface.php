<?php

namespace AppBundle\Services\Rotation\Filter;

use AppBundle\Entity\Preset;

/**
 * Interface FilterProviderInterface
 *
 * @package AppBundle\Services\Rotation\Filter
 */
interface FilterProviderInterface
{
    /**
     * @param Preset $preset
     * @return FilterCollection
     */
    public function getFilters(Preset $preset): FilterCollection;
}
