<?php

declare(strict_types=1);

namespace JD\DDD\Sales\Aggregate;

use JD\DDD\Sales\Entity\OrderProductItem;
use JD\DDD\Sales\Entity\Product;
use JD\DDD\Sales\ValueObject\CustomerId;
use JD\DDD\Sales\ValueObject\Money;
use JD\DDD\Sales\ValueObject\OrderId;
use JD\DDD\Sales\ValueObject\ProductId;
use JD\DDD\Sales\ValueObject\ProductQuantity;

final class Order
{
    public const MAX_ITEMS_IN_ORDER = 10;
    public const ORDER_TAX_PERCENTAGE = 0.2;

    private Money $subTotals;
    private Money $totals;

    /** @var OrderProductItem[] */
    private array $orderProductItems;

    private function __construct(
        public readonly OrderId $orderId,
        public readonly CustomerId $customerId,
    ) {
        $this->orderProductItems = [];
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
    }

    private function assertStockQuantity(Product $product, ProductQuantity $qty): void
    {
        if ($product->getProductQuantityInStock()->getQty() < $qty->getQty()) {
            throw new \DomainException('There is no such amount of product in stock');
        }
    }
}
