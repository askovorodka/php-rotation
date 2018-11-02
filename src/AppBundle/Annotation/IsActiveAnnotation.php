<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class IsActiveAnnotation
 * @package AppBundle\Annotation
 * @Annotation
 * @Target("CLASS")
 */
class IsActiveAnnotation
{
    public $fieldName;
}
