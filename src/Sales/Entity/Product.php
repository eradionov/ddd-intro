<?php

declare(strict_types=1);

namespace JD\DDD\Sales\Entity;

use JD\DDD\Sales\ValueObject\Money;
use JD\DDD\Sales\ValueObject\ProductDescription;
use JD\DDD\Sales\ValueObject\ProductId;
use JD\DDD\Sales\ValueObject\ProductName;
use JD\DDD\Sales\ValueObject\ProductQuantity;

final class Product
{
    private function __construct(
        public readonly ProductId $productId,
        private ProductName $productName,
        private Money $productPrice,
        private ProductQuantity $productQuantityInStock,
        private ?ProductDescription $productDescription = null,
    ) {
    }

    public static function create(
        ProductId $productId,
        ProductName $productName,
        Money $productPrice,
        ProductQuantity $productQuantity,
        ?ProductDescription $productDescription = null,
    ) {
        return new self(
            $productId,
            $productName,
            $productPrice,
            $productQuantity,
            $productDescription,
        );
    }

    public function getProductPrice(): Money
    {
        return $this->productPrice;
    }

    public function changeProductName(ProductName $productName): void
    {
        $this->productName = $productName;
    }

    public function changeQuantity(ProductQuantity $productQuantity): void
    {
        $this->productQuantityInStock = $productQuantity;
    }

    public function changeDescription(ProductDescription $description): void
    {
        $this->productDescription = $description;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getProductName(): ProductName
    {
        return $this->productName;
    }

    public function getProductQuantityInStock(): ProductQuantity
    {
        return $this->productQuantityInStock;
    }

    public function getProductDescription(): ?ProductDescription
    {
        return $this->productDescription;
    }
}
