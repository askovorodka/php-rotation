<?php

namespace AppBundle\Filter;


use AppBundle\Annotation\IsProductionAnnotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class IsProductionFilter extends SQLFilter
{

    const IS_PRODUCTION_TRUE = 1;
    private $reader;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        try {
            if (null === $this->reader) {
                return '';
            }
            $isProductionAnnotation = $this->reader->getClassAnnotation($targetEntity->getReflectionClass(),
                IsProductionAnnotation::class);
            if (!$isProductionAnnotation) {
                return '';
            }

            $fieldName = $isProductionAnnotation->fieldName;
            if (empty($fieldName)) {
                return '';
            }

            return sprintf('%s.%s = %s', $targetTableAlias, $fieldName, self::IS_PRODUCTION_TRUE);

        } catch (\Exception $exception) {
            return '';
        }
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
