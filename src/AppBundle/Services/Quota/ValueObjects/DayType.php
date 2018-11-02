<?php

namespace AppBundle\Services\Quota\ValueObjects;

/**
 * Тип дней [будни|выходные].
 * Может применяться при редактировании квот.
 *
 * Class DayType
 *
 * @package Domain\Quote\ValueObjects
 */
class DayType
{
    public const WEEKDAY = 'weekday';
    public const DAY_OFF = 'day_off';
    public const ANY = 'all';

    /**
     * @var string
     */
    private $value;

    /**
     * DayType constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!\in_array($value, $this->getChoices(), true)) {
            throw new \InvalidArgumentException(
                'value must be value of array [' . implode(',', $this->getChoices()) . ']'
            );
        }

        $this->value = $value;
    }

    /**
     * Тип буднего дня недели
     *
     * @return bool
     */
    public function isWeekday(): bool
    {
        return $this->value === self::WEEKDAY;
    }

    /**
     * Тип выходного дня недели
     *
     * @return bool
     */
    public function isDayOff(): bool
    {
        return $this->value === self::DAY_OFF;
    }

    /**
     * Тип любого дня недели
     *
     * @return bool
     */
    public function isAny(): bool
    {
        return $this->value === self::ANY;
    }

    /**
     * @return array
     */
    private function getChoices(): array
    {
        return [
            self::WEEKDAY,
            self::DAY_OFF,
            self::ANY,
        ];
    }

}
