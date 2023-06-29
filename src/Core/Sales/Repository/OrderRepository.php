<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\Repository;

use JD\DDD\Core\Sales\Aggregate\Order;
use JD\DDD\Core\Sales\ValueObject\OrderId;

class OrderRepository
{
    public function save(Order $order): void
    {
        // Save order
    }

    public function findOneById(OrderId $orderId): ?Order
    {
        // Should return Order
        return null;
    }
}
