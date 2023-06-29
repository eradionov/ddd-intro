<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\Service;

use JD\DDD\Core\Sales\Aggregate\Order;
use JD\DDD\Core\Sales\Entity\Product;
use JD\DDD\Core\Sales\Repository\OrderRepository;
use JD\DDD\Core\Sales\ValueObject\OrderId;
use JD\DDD\Core\Sales\ValueObject\ProductId;
use JD\DDD\Core\Sales\ValueObject\ProductQuantity;
use JD\DDD\Mocks\Repository\ProductRepository;

final readonly class OrderApplication
{
    public function __construct(
        private OrderRepository   $orderRepository,
        private ProductRepository $productRepository,
    ) {
    }

    public function addOrderLineToOrder(OrderId $orderId, ProductId $productId, ProductQuantity $quantity): void
    {
        /** @var Order|null $order */
        $order = $this->orderRepository->findOneById($orderId);

        /** @var Product|null $product */
        $product = $this->productRepository->findOneById($productId);

        if (null === $order || null === $product) {
            throw new \DomainException();
        }

        // Persisting of aggregate should be done as atomic operation.
        // Only 1 aggregate per transaction.
        // Aggregate defines transaction boundary
        $order->addOrderProductItem($product, $quantity);
        $this->orderRepository->save($order);
        $domainEvents = $order->releaseEvents();

        // domain events are published to notify all interested sub-domains
    }
}
