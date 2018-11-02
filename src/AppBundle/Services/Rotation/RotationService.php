<?php

namespace AppBundle\Services\Rotation;

use AppBundle\Services\Rotation\Dto\PresetBuilderDto;
use AppBundle\Services\Rotation\Dto\RotationDto;
use AppBundle\Services\Rotation\Filter\FilterProviderInterface;
use AppBundle\Services\Rotation\Order\OrderProviderInterface;
use AppBundle\Services\Rotation\Pin\PinProviderInterface;
use AppBundle\Services\Rotation\Preset\Exception\PresetNotFoundException;
use AppBundle\Services\Rotation\Preset\PresetProviderInterface;

/**
 * Class RotationService
 *
 * @package AppBundle\Services\Rotation
 */
class RotationService implements RotationServiceInterface
{
    /** @var PresetProviderInterface  */
    private $presetProvider;

    /** @var PinProviderInterface */
    private $pinProvider;

    /** @var FilterProviderInterface */
    private $filterProvider;

    /** @var OrderProviderInterface */
    private $orderProvider;

    /** @var RotationBuilderInterface */
    private $rotationBulder;

    public function __construct(
        PresetProviderInterface $presetProvider,
        PinProviderInterface $pinProvider,
        FilterProviderInterface $filterProvider,
        OrderProviderInterface $orderProvider,
        RotationBuilderInterface $rotationBuilder)
    {
        $this->presetProvider = $presetProvider;
        $this->pinProvider = $pinProvider;
        $this->filterProvider = $filterProvider;
        $this->orderProvider = $orderProvider;
        $this->rotationBulder = $rotationBuilder;
    }

    public function getPreset(RotationDto $rotationDto): array
    {
        if ($rotationDto->presetSysname) {
            $presetEntity = $this->presetProvider->getPresetBySysname($rotationDto->presetSysname);
        } else {
            $presetEntity = $this->presetProvider->getPreset(
                $rotationDto->presetCategorySysname,
                $rotationDto->presetCityId,
                $rotationDto->presetRegionId);
        }
        if (!$presetEntity) {
            throw new PresetNotFoundException();
        }

        $pinsCollection = $this->pinProvider->getPins(
            $rotationDto->presetCityId,
            $rotationDto->presetRegionId,
            $presetEntity->getId());

        $filterCollection = $this->filterProvider->getFilters($presetEntity);
        $orderCollection = $this->orderProvider->getOrders($presetEntity);

        $catalog = $this->rotationBulder->getCatalog($filterCollection, $orderCollection, $pinsCollection);

        return [
            'banners' => $catalog->getBanners(),
            'items' => $catalog->getItems(),
        ];
    }

}
