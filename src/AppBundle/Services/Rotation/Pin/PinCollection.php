<?php

namespace AppBundle\Services\Rotation\Pin;

use AppBundle\Services\Rotation\Pin\Dto\PinDto;

/**
 * Class PinCollection
 *
 * @package AppBundle\Services\Rotation\Pin
 */
class PinCollection implements \Iterator
{
    /**
     * @var PinDto[]
     */
    private $pins = [];

    /**
     * @var PinDto[][]
     */
    private $pinsByPosition = [];

    /**
     * @var PinDto[][]
     */
    private $pinsByPinnedHotelId = [];

    /**
     * @var PinDto[][]
     */
    private $pinsByPinnedPresetId = [];

    /**
     * @param PinDto $pin
     * @return PinCollection
     */
    public function add(PinDto $pin): self
    {
        $this->pins[] = $pin;

        $this->pinsByPosition[$pin->category][$pin->position] = $pin;

        if ($pin->pinnedHotelId !== null) {
            $this->pinsByPinnedHotelId[$pin->category][$pin->pinnedHotelId] = $pin;
        }

        if ($pin->pinnedPresetId !== null) {
            $this->pinsByPinnedPresetId[$pin->category][$pin->pinnedPresetId] = $pin;
        }

        return $this;
    }

    /**
     * @param string $category
     * @param int    $position
     * @return PinDto|null
     */
    public function getPinByPosition(string $category, int $position): ?PinDto
    {
        return $this->pinsByPosition[$category][$position] ?? null;
    }

    /**
     * @param string $category
     * @param int    $pinnedHotelId
     * @return PinDto|null
     */
    public function getPinByPinnedHotelId(string $category, int $pinnedHotelId): ?PinDto
    {
        return $this->pinsByPinnedHotelId[$category][$pinnedHotelId] ?? null;
    }

    /**
     * @param string $category
     * @param int    $pinnedPresetId
     * @return PinDto|null
     */
    public function getPinByPinnedPresetId(string $category, int $pinnedPresetId): ?PinDto
    {
        return $this->pinsByPinnedPresetId[$category][$pinnedPresetId] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->pins);
    }

    /**
     * @inheritdoc
     */
    public function current(): PinDto
    {
        return current($this->pins);
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        next($this->pins);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->pins);
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return key($this->pins) !== null;
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        reset($this->pins);
    }
}
