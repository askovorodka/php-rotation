<?php

namespace AppBundle\Validator\Hotel;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HotelIsProduction extends Constraint
{
    public $message = 'Этот отель не может быть isProduction';

    public function validatedBy(): string
    {
        return HotelIsProductionValidator::class;
    }
}
