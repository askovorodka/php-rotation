<?php

namespace AppBundle\Services\Quota;

use AppBundle\Services\Quota\ValueObjects\DayType;
use AppBundle\Entity\Quota;
use AppBundle\Entity\Room;

class QuotasEditorService implements QuotasEditorServiceInterface
{
    /**
     * @inheritdoc
     */
    public function createMissedQuotasByInterval(
        Room $room,
        \DateTimeInterface $beginDate,
        \DateTimeInterface $endDate,
        array $existingQuotas
    ): array {
        $createdQuotas = [];

        $existingQuotasWithDateKey = [];
        /** @var Quota $existingQuota */
        foreach ($existingQuotas as $existingQuota) {
            $dateKey = $existingQuota->getDate()->format('d.m.Y');
            $existingQuotasWithDateKey[$dateKey] = $existingQuota;
        }

        $dateInterval = \DateInterval::createFromDateString('1 day');
        $endDate = (new \DateTime($endDate->format('Y-m-d')))->modify('+1 day');

        $period = new \DatePeriod($beginDate, $dateInterval, $endDate);

        /** @var \DateTimeInterface $date */
        foreach ($period as $date) {
            $dateKey = $date->format('d.m.Y');

            if (array_key_exists($dateKey, $existingQuotasWithDateKey)) {
                continue;
            }

            $quota = new Quota();
            $quota->setRoom($room)->setDate($date);

            $createdQuotas[] = $quota;
        }

        return $createdQuotas;
    }

    /**
     * @inheritdoc
     */
    public function increaseQuantityInQuotas(
        array $quotas,
        int $delta,
        DayType $dayType = null
    ): array {
        $affectedQuotas = [];

        $dayType = $dayType ?? new DayType(DayType::ANY);

        /** @var Quota $quota */
        foreach ($quotas as $quota) {
            if ($dayType->isAny()) {
                $quota->increaseQuantity($delta);
                $affectedQuotas[] = $quota;
                continue;
            }

            if ($dayType->isDayOff() && $this->isDayOffQuota($quota)) {
                $quota->increaseQuantity($delta);
                $affectedQuotas[] = $quota;
                continue;
            }

            if ($dayType->isWeekday() && $this->isWeekdayQuota($quota)) {
                $quota->increaseQuantity($delta);
                $affectedQuotas[] = $quota;
                continue;
            }
        }

        return $affectedQuotas;
    }

    /**
     * @inheritdoc
     */
    public function decreaseQuantityInQuotas(
        array $quotas,
        int $delta,
        DayType $dayType = null
    ): array {
        $affectedQuotas = [];

        $dayType = $dayType ?? new DayType(DayType::ANY);

        /** @var Quota $quota */
        foreach ($quotas as $quota) {
            if ($dayType->isAny()) {
                $quota->decreaseQuantity($delta);
                $affectedQuotas[] = $quota;
                continue;
            }

            if ($dayType->isDayOff() && $this->isDayOffQuota($quota)) {
                $quota->decreaseQuantity($delta);
                $affectedQuotas[] = $quota;
                continue;
            }

            if ($dayType->isWeekday() && $this->isWeekdayQuota($quota)) {
                $quota->decreaseQuantity($delta);
                $affectedQuotas[] = $quota;
                continue;
            }
        }

        return $affectedQuotas;
    }

    /**
     * Квота выходного дня
     *
     * @param Quota $quota
     * @return bool
     */
    private function isDayOffQuota(Quota $quota): bool
    {
        return $quota->getDate()->format('N') >= 6;
    }

    /**
     * Квота буднего дня
     *
     * @param Quota $quota
     * @return bool
     */
    private function isWeekdayQuota(Quota $quota): bool
    {
        return $quota->getDate()->format('N') <= 5;
    }
}
