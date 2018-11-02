<?php

namespace AppBundle\Filter;

use AppBundle\Annotation\IsActiveAnnotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class IsActiveFilter
 * @package AppBundle\Filter
 */
class IsActiveFilter extends SQLFilter
{
    private $reader;
    const IS_ACTIVE_TRUE = 1;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        try {
            if (null === $this->reader) {
                return '';
            }

            $isActiveAnnotation = $this->reader->getClassAnnotation($targetEntity->getReflectionClass(),
                IsActiveAnnotation::class);

            if (!$isActiveAnnotation) {
                return '';
            }

            $fieldName = $isActiveAnnotation->fieldName;
            if (empty($fieldName)) {
                return '';
            }

            return sprintf('%s.%s = %s', $targetTableAlias, $fieldName, self::IS_ACTIVE_TRUE);

        } catch (\Exception $exception) {
            return '';
        }
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
