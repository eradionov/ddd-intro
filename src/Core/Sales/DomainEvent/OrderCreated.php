<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\DomainEvent;

use JD\DDD\Common\DomainEventInterface;
use JD\DDD\Core\Sales\ValueObject\Money;
use JD\DDD\Core\Sales\ValueObject\OrderId;

final class OrderCreated implements DomainEventInterface
{
    public function __construct(
        private readonly OrderId $orderId,
        public readonly Money $subTotals,
        public readonly Money $totals,
    ) {
    }

    public function getId(): string
    {
        return $this->orderId->getId();
    }
}
