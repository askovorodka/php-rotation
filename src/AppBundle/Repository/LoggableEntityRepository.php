<?php

namespace AppBundle\Repository;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameGenerator;
use AppBundle\Annotation\LoggableTableTitleAnnotation;
use AppBundle\ApiResource\LoggableEntity;
use AppBundle\Entity\LogEntry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Gedmo\Mapping\Annotation\Loggable;

/**
 * Class LoggableEntityRepository
 *
 * @package AppBundle\Repository
 */
class LoggableEntityRepository
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * LoggableEntityRepository constructor.
     *
     * @param ManagerRegistry       $managerRegistry
     * @param Reader                $annotationsReader
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        Reader $annotationsReader,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->annotationReader = $annotationsReader;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Возвращает массив всех логгируемых сущностей
     *
     * @return LoggableEntity[]
     */
    public function findAll()
    {
        $em = $this->managerRegistry->getManager();

        $classes = [];
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metaData */
        foreach ($em->getMetadataFactory()->getAllMetadata() as $metaData) {
            $entityAnnotations = $this->annotationReader->getClassAnnotations($metaData->getReflectionClass());
            if (!empty($entityAnnotations)) {
                $className = null;
                $title = null;
                foreach ($entityAnnotations as $entityAnnotation) {
                    if (
                        $entityAnnotation instanceof Loggable
                        && $entityAnnotation->logEntryClass === LogEntry::class
                    ) {
                        $className = $metaData->getReflectionClass()->name;
                    }
                    if ($entityAnnotation instanceof LoggableTableTitleAnnotation) {
                        $title = $entityAnnotation->title;
                    }
                }
                if ($className) {
                    $classes[$className] = $title;
                }
            }
        }

        $routeName = RouteNameGenerator::generate('get', $this->getShortClassName(LogEntry::class), 'collection');

        $result = [];
        foreach ($classes as $className => $title) {
            $loggableEntity = new LoggableEntity($className);
            $loggableEntity->setLogRecordsLink($this->urlGenerator->generate($routeName) . '?objectClass=' . $className);
            $loggableEntity->setTitle($title ?? $this->getShortClassName($className));
            $result[] = $loggableEntity;
        }

        return $result;
    }

    /**
     * @param string $className
     * @return mixed
     */
    protected function getShortClassName(string $className)
    {
        $className = explode('\\', $className);
        return end($className);
    }
}
