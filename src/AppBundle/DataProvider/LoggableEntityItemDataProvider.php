<?php

namespace AppBundle\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use AppBundle\ApiResource\LoggableEntity;
use AppBundle\Repository\LoggableEntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Класс, предоставляющий данные для loggable-entities [API]
 *
 * @package AppBundle\DataProvider
 */
final class LoggableEntityItemDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var LoggableEntityRepository */
    protected $loggableEntityRepository;

    /**
     * @param LoggableEntityRepository $loggableEntityRepository
     */
    public function __construct(LoggableEntityRepository $loggableEntityRepository)
    {
        $this->loggableEntityRepository = $loggableEntityRepository;
    }

    /**
     * @inheritdoc
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return
            $resourceClass === LoggableEntity::class
            && in_array($operationName, ['get_loggable_entity', 'get_loggable_entities']);
    }

    /**
     * @inheritdoc
     *
     * @return LoggableEntity
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        foreach ($this->loggableEntityRepository->findAll() as $loggableEntity) {
            if ($loggableEntity->getName() === $id) {
                return $loggableEntity;
            }
        }

        throw new NotFoundHttpException('Not found');
    }

    /**
     * @inheritdoc
     *
     * @return LoggableEntity[]|array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        return $this->loggableEntityRepository->findAll();
    }
}
