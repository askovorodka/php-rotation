<?php

namespace AppBundle\Services\Rotation\Order;

use AppBundle\Services\Rotation\Order\Dto\OrderDto;

/**
 * Class OrderCollection
 *
 * @package AppBundle\Services\Rotation\Order
 */
class OrderCollection implements \Iterator
{
    /**
     * @var OrderDto[][]
     */
    private $ordersIndexedByNum = [];

    /**
     * @param OrderDto $order
     * @return $this
     */
    public function add(OrderDto $order): self
    {
        $this->ordersIndexedByNum[$order->num][] = $order;
        return $this;
    }

    /**
     * @inheritdoc
     * @return OrderDto[]
     */
    public function current(): array
    {
        return current($this->ordersIndexedByNum);
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        next($this->ordersIndexedByNum);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->ordersIndexedByNum);
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return key($this->ordersIndexedByNum) !== null;
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        reset($this->ordersIndexedByNum);
    }
}
