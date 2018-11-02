<?php

namespace AppBundle\Services\Rotation\Pin;

use AppBundle\Entity\FederalHotelPin;
use AppBundle\Entity\LocalHotelPin;
use AppBundle\Entity\PresetPin;
use AppBundle\Repository\FederalHotelPinRepository;
use AppBundle\Repository\LocalHotelPinRepository;
use AppBundle\Repository\PresetPinRepository;
use AppBundle\Services\Rotation\Pin\Dto\PinDto;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class PinProvider
 *
 * @package AppBundle\Services\Rotation\Pin
 */
class PinProvider implements PinProviderInterface
{
    /**
     * @var FederalHotelPinRepository
     */
    private $federalHotelPinRepository;

    /**
     * @var LocalHotelPinRepository
     */
    private $localHotelPinRepository;

    /**
     * @var PresetPinRepository
     */
    private $presetPinRepository;

    /**
     * PinProvider constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->federalHotelPinRepository = $managerRegistry->getRepository(FederalHotelPin::class);
        $this->localHotelPinRepository = $managerRegistry->getRepository(LocalHotelPin::class);
        $this->presetPinRepository = $managerRegistry->getRepository(PresetPin::class);
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\ORM\ORMException
     */
    public function getPins(?int $cityId, ?int $regionId, int $presetId): PinCollection
    {
        $pinCollection = new PinCollection();

        $this->addFederalHotelPins($cityId, $regionId, $pinCollection);
        $this->addLocalHotelPins($presetId, $cityId, $regionId, $pinCollection);
        $this->addPresetPins($presetId, $cityId, $regionId, $pinCollection);

        return $pinCollection;
    }

    /**
     * @param int           $cityId
     * @param int           $regionId
     * @param PinCollection $pinCollection
     */
    private function addFederalHotelPins(?int $cityId, ?int $regionId, PinCollection $pinCollection): void
    {
        $federalHotelPins = $this->federalHotelPinRepository->getActivePins();

        /** @var FederalHotelPin $federalHotelPin */
        foreach ($federalHotelPins as $federalHotelPin) {
            $excludeCities = $federalHotelPin->getExcludeCitiesIds();
            $excludeRegions = $federalHotelPin->getExcludeRegionsIds();

            if (\is_array($excludeCities) && \in_array($cityId, $excludeCities, false)) {
                continue;
            }

            if (\is_array($excludeRegions) && \in_array($regionId, $excludeRegions, false)) {
                continue;
            }

            $pin = new PinDto(
                $federalHotelPin->getPinCategory()->getSysname(),
                $federalHotelPin->getPosition()
            );
            $pin->pinnedHotelId = $federalHotelPin->getHotel()->getId();

            $pinCollection->add($pin);
        }
    }

    /**
     * @param int           $presetId
     * @param int           $cityId
     * @param int           $regionId
     * @param PinCollection $pinCollection
     * @throws \Doctrine\ORM\ORMException
     */
    private function addLocalHotelPins(?int $presetId, ?int $cityId, ?int $regionId, PinCollection $pinCollection): void
    {
        $localHotelPins = $this->localHotelPinRepository->getActivePins($presetId, $cityId, $regionId);

        foreach ($localHotelPins as $localHotelPin) {
            $pin = new PinDto(
                $localHotelPin->getPinCategory()->getSysname(),
                $localHotelPin->getPosition()
            );
            $pin->pinnedHotelId = $localHotelPin->getHotel()->getId();

            $pinCollection->add($pin);
        }
    }

    /**
     * @param int           $presetId
     * @param int           $cityId
     * @param int           $regionId
     * @param PinCollection $pinCollection
     * @throws \Doctrine\ORM\ORMException
     */
    private function addPresetPins(?int $presetId, ?int $cityId, ?int $regionId, PinCollection $pinCollection): void
    {
        $presetPins = $this->presetPinRepository->getActivePins($presetId, $cityId, $regionId);

        foreach ($presetPins as $presetPin) {
            $pin = new PinDto(
                $presetPin->getPinCategory()->getSysname(),
                $presetPin->getPosition()
            );
            $pin->pinnedPresetId = $presetPin->getPinnedPreset()->getId();

            $pinCollection->add($pin);
        }
    }

}
