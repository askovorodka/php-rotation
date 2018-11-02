<?php

namespace AppBundle\Services\Rotation\Order;

use AppBundle\Entity\Preset;
use AppBundle\Services\Rotation\Order\Dto\OrderDto;

/**
 * Interface OrderProviderInterface
 *
 * @package AppBundle\Services\Rotation\Order
 */
interface OrderProviderInterface
{
    /**
     * @param Preset     $preset
     * @param OrderDto[] $extraOrders
     * @return OrderCollection
     */
    public function getOrders(Preset $preset, array $extraOrders = []): OrderCollection;
}
