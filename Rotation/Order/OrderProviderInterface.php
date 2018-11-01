<?php

namespace AppBundle\Services\Rotation\Order;

use AppBundle\Entity\Preset;

/**
 * Interface OrderProviderInterface
 *
 * @package AppBundle\Services\Rotation\Order
 */
interface OrderProviderInterface
{
    /**
     * @param Preset   $preset
     * @param string[] $additionalFields
     * @return OrderCollection
     */
    public function getOrders(Preset $preset, array $additionalFields): OrderCollection;
}
