<?php

namespace AppBundle\Services\Rotation;

use AppBundle\Services\Rotation\Filter\FilterCollection;
use AppBundle\Services\Rotation\Order\OrderCollection;
use AppBundle\Services\Rotation\Pin\PinCollection;

/**
 * Interface RotationBuilderInterface
 *
 * @package AppBundle\Services\Rotation
 */
interface RotationBuilderInterface
{
    /**
     * @param FilterCollection $filterCollection
     * @param OrderCollection  $orderCollection
     * @param PinCollection    $pinCollection
     * @return Catalog
     */
    public function getCatalog(
        FilterCollection $filterCollection,
        OrderCollection $orderCollection,
        PinCollection $pinCollection
    ): Catalog;

}
