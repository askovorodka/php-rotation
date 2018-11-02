<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class IsProductionAnnotation
 * @package AppBundle\Annotation
 * @Annotation
 * @Target("CLASS")
 */
class IsProductionAnnotation
{
    public $fieldName;
}
