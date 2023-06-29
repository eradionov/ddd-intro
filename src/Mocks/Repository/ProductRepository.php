<?php

declare(strict_types=1);

namespace JD\DDD\Mocks\Repository;

use JD\DDD\Core\Sales\Entity\Product;
use JD\DDD\Core\Sales\ValueObject\ProductId;

class ProductRepository
{
    public function findOneById(ProductId $productId): ?Product
    {
        // Should return Order
        return null;
    }
}