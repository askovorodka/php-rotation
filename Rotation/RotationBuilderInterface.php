<?php

namespace AppBundle\Services\Rotation;

use AppBundle\Services\Rotation\Filter\FilterCollection;
use AppBundle\Services\Rotation\Order\OrderCollection;
use AppBundle\Services\Rotation\Pin\PinCollection;

interface RotationBuilderInterface
{
    /**
     * @param FilterCollection $filterCollection
     * @param OrderCollection  $orderCollection
     * @param PinCollection    $pinCollection
     * @return mixed
     */
    public function getCatalog(
        FilterCollection $filterCollection,
        OrderCollection $orderCollection,
        PinCollection $pinCollection
    );

}
