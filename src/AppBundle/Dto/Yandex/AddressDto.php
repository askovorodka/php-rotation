<?php

namespace AppBundle\Dto\Yandex;

/**
 * DTO, предоставляющий данные для геокодинга
 *
 * @package AppBundle\Dto\Yandex
 */
class AddressDto
{
    /** @var string */
    public $country = '';
    /** @var string */
    public $administrativeArea = '';
    /** @var string */
    public $subAdministrativeArea = '';
    /** @var string */
    public $locality = '';
    /** @var string */
    public $thoroughfare = '';
    /** @var string */
    public $premise = '';
    /** @var string */
    public $postalCode = '';
}