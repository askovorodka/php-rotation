<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Аннотация для указания заголовка логгируемой таблицы
 *
 * @Annotation
 * @Target("CLASS")
 */
class LoggableTableTitleAnnotation extends Annotation
{
    public $title;
}