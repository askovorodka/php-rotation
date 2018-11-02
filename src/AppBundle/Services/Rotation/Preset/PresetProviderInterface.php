<?php

namespace AppBundle\Services\Rotation\Preset;

use AppBundle\Entity\Preset;
use AppBundle\Services\Rotation\Preset\Exception\PresetNotFoundException;

/**
 * Interface PresetProviderInterface
 *
 * @package AppBundle\Services\Preset
 */
interface PresetProviderInterface
{
    /**
     * @param string $presetCategorySysname
     * @param int    $cityId
     * @param int    $regionId
     * @return Preset
     * @throws PresetNotFoundException
     */
    public function getPreset(string $presetCategorySysname, ?int $cityId, ?int $regionId): Preset;

    /**
     * @param string $sysname
     * @return Preset
     * @throws PresetNotFoundException
     */
    public function getPresetBySysname(string $sysname): Preset;
}
