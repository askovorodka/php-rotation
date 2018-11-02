<?php

namespace AppBundle\Services\Rotation\Order;

use AppBundle\Entity\Preset;
use AppBundle\Services\Rotation\Order\Dto\OrderDto;

/**
 * Class OrderProvider
 *
 * @package AppBundle\Services\Rotation\Order
 */
class OrderProvider implements OrderProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getOrders(Preset $preset, array $extraOrders = []): OrderCollection
    {
        $orderCollection = new OrderCollection();

        // Если переданы дополнительные сортировки, учитываем только их без учета сортировок самой подборки
        if ($extraOrders) {
            foreach ($extraOrders as $extraOrder) {
                $orderCollection->add($extraOrder);
            }

            return $orderCollection;
        }

        $presetParams = $preset->getParams();

        $sorts = $presetParams['sorts'] ?? [];

        foreach ($sorts as $field => $rawOrders) {
            foreach ($rawOrders as $rawOrder) {
                $num = $rawOrder['order'] ?? null;

                if ($field && $num !== null) {
                    $orderDto = new OrderDto($num, $field);

                    $value = $rawOrder['value'] ?? null;
                    if ($value !== null) {
                        $orderDto->value = $value;
                    }

                    $direction = $rawOrder['direction'] ?? null;
                    if ($direction == 'asc' || $direction == 'desc') {
                        $orderDto->direction = $direction;
                    }

                    $orderCollection->add($orderDto);
                }
            }
        }

        return $orderCollection;
    }
}
