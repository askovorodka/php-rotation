<?php

namespace AppBundle\Services\Rotation\Order;

use AppBundle\Services\Rotation\Order\Dto\OrderDto;

class OrderCollection
{
    /**
     * @var OrderDto[]
     */
    private $orders = [];

    /**
     * @param OrderDto $order
     * @return $this
     */
    public function add(OrderDto $order): self
    {
        $this->orders[] = $order;
        return $this;
    }
}
