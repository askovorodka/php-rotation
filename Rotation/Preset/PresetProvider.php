<?php

namespace AppBundle\Services\Rotation\Preset;

use AppBundle\Entity\Preset;
use AppBundle\Repository\PresetRepository;
use AppBundle\Services\Preset\PresetProviderInterface;
use AppBundle\Services\Rotation\Preset\Exception\PresetNotFoundException;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class PresetProvider
 *
 * @package AppBundle\Services\Rotation\Preset
 */
class PresetProvider implements PresetProviderInterface
{
    /**
     * @var PresetRepository
     */
    private $presetRepository;

    /**
     * PresetProvider constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->presetRepository = $managerRegistry->getRepository(Preset::class);
    }

    /**
     * @inheritdoc
     */
    public function getPreset(string $presetCategorySysname, int $cityId, int $regionId): Preset
    {
        $preset = $this->presetRepository->getActivePreset($presetCategorySysname, $cityId, $regionId);

        if (!$preset) {
            throw new PresetNotFoundException();
        }

        return $preset;
    }

    /**
     * @inheritdoc
     */
    public function getPresetBySysname(string $sysname): Preset
    {
        $preset = $this->presetRepository->getActivePresetBySysname($sysname);

        if (!$preset) {
            throw new PresetNotFoundException();
        }

        return $preset;
    }

}
