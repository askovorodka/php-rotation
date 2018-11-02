<?php

namespace AppBundle\Services\Quota;

use AppBundle\Exception\QuantityCannotBeDecreasedException;
use AppBundle\Services\Quota\ValueObjects\DayType;
use AppBundle\Entity\Quota;
use AppBundle\Entity\Room;

interface QuotasEditorServiceInterface
{
    /**
     * Создает квоты для комнаты в интервале дат, если их нет в массиве существующих квот
     *
     * @param Room               $room
     * @param \DateTimeInterface $beginDate
     * @param \DateTimeInterface $endDate
     * @param Quota[]            $existingQuotas
     *
     * @return Quota[] created quotas
     */
    public function createMissedQuotasByInterval(
        Room $room,
        \DateTimeInterface $beginDate,
        \DateTimeInterface $endDate,
        array $existingQuotas
    ): array;

    /**
     * Добавляет места в квоты
     *
     * @param Quota[]      $quotas
     * @param int          $delta
     * @param DayType|null $dayType
     *
     * @return Quota[] affected quotas
     */
    public function increaseQuantityInQuotas(
        array $quotas,
        int $delta,
        DayType $dayType = null
    ): array;

    /**
     * Уменьшает места в квотах.
     * Кол-во свободных мест в квоте должно быть больше или равно значению,
     * на которое уменьшается кол-во мест.
     *
     * @param Quota[]      $quotas
     * @param int          $delta
     * @param DayType|null $dayType
     *
     * @return Quota[] affected quotas
     *
     * @throws QuantityCannotBeDecreasedException
     */
    public function decreaseQuantityInQuotas(
        array $quotas,
        int $delta,
        ?DayType $dayType = null
    ): array;
}
