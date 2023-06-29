<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\Aggregate;

use JD\DDD\Common\DomainEventInterface;
use JD\DDD\Core\Sales\DomainEvent\OrderCreated;
use JD\DDD\Core\Sales\DomainEvent\OrderTotalsRecalculated;
use JD\DDD\Core\Sales\Entity\OrderProductItem;
use JD\DDD\Core\Sales\Entity\Product;
use JD\DDD\Core\Sales\ValueObject\Currency;
use JD\DDD\Core\Sales\ValueObject\CustomerId;
use JD\DDD\Core\Sales\ValueObject\Money;
use JD\DDD\Core\Sales\ValueObject\OrderId;
use JD\DDD\Core\Sales\ValueObject\ProductId;
use JD\DDD\Core\Sales\ValueObject\ProductQuantity;

// Modifications to OrderProductItem is done only via aggregate root
final class Order
{
    public const MAX_ITEMS_IN_ORDER = 10;
    public const ORDER_TAX_PERCENTAGE = 0.2;

    private int $version;

    private Money $subTotals;
    private Money $totals;

    /** @var DomainEventInterface[] */
    private array $domainEvents;

    /** @var OrderProductItem[] */
    private array $orderProductItems;
    private function __construct(
        public readonly OrderId $orderId,
        public readonly CustomerId $customerId,
    ) {
        $this->orderProductItems = [];
        $this->totals = Money::fromCurrency(Currency::fromUSD());
        $this->subTotals = Money::fromCurrency(Currency::fromUSD());
        $this->version = 1;

        // @info: add totals recalculated domain event
        $this->domainEvents = [new OrderCreated($this->orderId, $this->subTotals, $this->totals)];
    }

    public static function create(
        OrderId $orderId,
        CustomerId $customerId,
    ): self {
        return new self(
            $orderId,
            $customerId,
        );
    }

    /**
     * @throws \DomainException
     */
    public function addOrderProductItem(Product $product, ProductQuantity $qty): void
    {
        $this->assertMaxQuantity($qty);
        $this->assertStockQuantity($product, $qty);
        $this->assertCurrencyConsistency($product);

        $productId = $product->getProductId();
        $itemAlreadyInOrder = false;

        foreach ($this->orderProductItems as $key => $orderProductItem) {
            if ($orderProductItem->getProductId()->equals($productId)) {
                $this->orderProductItems[$key] = $this->orderProductItems[$key]->changeQuantity($qty);
                $itemAlreadyInOrder = true;
                break;
            }
        }

        if (!$itemAlreadyInOrder) {
            $this->orderProductItems[] = OrderProductItem::create($productId, $product->getProductPrice(), $qty);
        }

        $this->recalculateTotals();
    }

    public function removeOrderItem(ProductId $productId): void
    {
        foreach ($this->orderProductItems as $key => $orderProductItem) {
            if ($orderProductItem->getProductId()->equals($productId)) {
                unset($this->orderProductItems[$key]);
            }
        }

        $this->recalculateTotals();
    }

    public function getTotals(): Money
    {
        return $this->totals;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return OrderProductItem[]
     */
    public function getOrderProductItems(): array
    {
        return $this->orderProductItems;
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getSubTotals(): Money
    {
        return $this->subTotals;
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    private function assertMaxQuantity(ProductQuantity $orderProductQuantity): void
    {
        $totalItemQty = (int) \array_reduce(
            $this->orderProductItems,
            static fn (int|null $qty, OrderProductItem $orderItem): int => (int) $qty + $orderItem->getQty()->getQty()
        );

        if ($totalItemQty + $orderProductQuantity->getQty() > self::MAX_ITEMS_IN_ORDER) {
            throw new \DomainException(
                \sprintf('Order max items \'%s\' number exceeded', self::MAX_ITEMS_IN_ORDER)
            );
        }
    }

    private function assertCurrencyConsistency(Product $product): void
    {
        if (0 === \count($this->orderProductItems)) {
            return;
        }

        foreach ($this->orderProductItems as $orderProductItem) {
            if (!$orderProductItem->getLinePrice()->getCurrency()->equals($product->getProductPrice()->getCurrency())) {
                throw new \DomainException('Order should not contain items in different currency prices');
            }
        }
    }

    private function recalculateTotals(): void
    {
        if (0 === \count($this->orderProductItems)) {
            throw new \DomainException('Order should contain at least 1 item');
        }

        $calculatedPrice = 0;

        $currency = $this->orderProductItems[0]->getLinePrice()->getCurrency();

        foreach ($this->orderProductItems as $orderProductItem) {
            $calculatedPrice += $orderProductItem->getLinePrice()->getAmount();
        }

        $this->subTotals = Money::fromAmountAndCurrency(
            $calculatedPrice,
            $currency
        );

        $this->totals = Money::fromAmountAndCurrency(
            (int) \ceil(($calculatedPrice * self::ORDER_TAX_PERCENTAGE) + $calculatedPrice),
            $currency
        );

        // @info: add totals recalculated domain event
        $this->domainEvents[] = new OrderTotalsRecalculated($this->orderId, $this->subTotals, $this->totals);
    }

    private function assertStockQuantity(Product $product, ProductQuantity $qty): void
    {
        if ($product->getProductQuantityInStock()->getQty() < $qty->getQty()) {
            throw new \DomainException('There is no such amount of product in stock');
        }
    }

    /**
     * @return DomainEventInterface[]
    */
    public function releaseEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }
}
