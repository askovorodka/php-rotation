<?php

namespace AppBundle\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use AppBundle\Entity\DealOfferPrice;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * Базовый CollectionDataProvider-класс
 *
 * @package AppBundle\DataProvider
 */
abstract class AbstractCollectionDataProvider
{
    /** @var ManagerRegistry */
    protected $managerRegistry;
    /** @var ContextAwareQueryCollectionExtensionInterface[]|QueryCollectionExtensionInterface[]|array */
    protected $collectionExtensions;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param QueryCollectionExtensionInterface[]|ContextAwareQueryCollectionExtensionInterface[] $collectionExtensions
     */
    public function __construct(ManagerRegistry $managerRegistry, array $collectionExtensions = [])
    {
        $this->managerRegistry = $managerRegistry;
        $this->collectionExtensions = $collectionExtensions;
    }

    /**
     * @inheritdoc
     *
     * @return DealOfferPrice[]|\Traversable|array[]
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        if (!($queryBuilder = $this->createQueryBuilder($resourceClass, $operationName, $context))) {
            return [];
        }

        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass,
                    $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     *
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    abstract protected function createQueryBuilder(
        string $resourceClass,
        string $operationName = null,
        array $context = []
    );
}
