<?php

declare(strict_types=1);

namespace JD\DDD\Sales\ValueObject;

use Assert\Assertion;

final class OrderProductItem
{
    public const MIN_PRODUCT_COUNT = 1;
    public const MAX_PRODUCT_COUNT = 10;

    private Money $linePrice;

    private function __construct(
        private ProductId $productId,
        private readonly Money $productPrice,
        private ProductQuantity $qty,
    ) {
        Assertion::between(
            $qty,
            self::MIN_PRODUCT_COUNT,
            self::MAX_PRODUCT_COUNT,
            \sprintf('Quantity should be in range \'%d\' to \'%d\'', self::MIN_PRODUCT_COUNT, self::MAX_PRODUCT_COUNT)
        );

        $this->linePrice = Money::fromAmountAndCurrency(
            $productPrice->getAmount() * $qty->getQty(),
            $productPrice->getCurrency()
        );
    }

    public static function create(
        ProductId $productId,
        Money $productPrice,
        ProductQuantity $qty,
    ): self {
        return new self(
            $productId,
            $productPrice,
            $qty,
        );
    }

    public function changeQuantity(ProductQuantity $qty): self
    {
        return new self(
            $this->productId,
            $this->productPrice,
            $qty,
        );
    }

    public function getProductPrice(): Money
    {
        return $this->productPrice;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getLinePrice(): Money
    {
        return $this->linePrice;
    }

    public function getQty(): ProductQuantity
    {
        return $this->qty;
    }
}
