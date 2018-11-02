<?php

namespace AppBundle\Services\Rotation\Preset\Exception;

/**
 * Class PresetNotFoundException
 *
 * @package AppBundle\Services\Rotation\Preset\Exception
 */
class PresetNotFoundException extends \Exception
{
    public function __construct(string $message = 'Preset not found', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
