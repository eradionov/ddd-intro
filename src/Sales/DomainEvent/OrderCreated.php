<?php

declare(strict_types=1);

namespace JD\DDD\Sales\DomainEvent;

use Exception;
use JD\DDD\Common\DomainEventInterface;
use JD\DDD\Sales\ValueObject\Money;
use JD\DDD\Sales\ValueObject\OrderId;

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