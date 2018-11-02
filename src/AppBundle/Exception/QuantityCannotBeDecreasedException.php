<?php

namespace AppBundle\Exception;

class QuantityCannotBeDecreasedException extends \Exception
{
    public static function withSlug(
        string $roomTitle,
        int $decreaseValue,
        \DateTimeInterface $date,
        int $freeQuantity = null
    ): self {
        $errorMessage = "Квота для комнаты $roomTitle для даты " . $date->format('d.m.Y') .
            " не может быть уменьшена на $decreaseValue.";

        if ($freeQuantity) {
            $errorMessage .= " Максимально доступное значение уменьшения $freeQuantity.";
        }

        return new self($errorMessage);
    }
}
