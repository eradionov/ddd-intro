<?php

declare(strict_types=1);

namespace Sales\Aggregate;

use JD\DDD\Core\Sales\Aggregate\Order;
use JD\DDD\Core\Sales\Entity\Product;
use JD\DDD\Core\Sales\ValueObject\Currency;
use JD\DDD\Core\Sales\ValueObject\CustomerId;
use JD\DDD\Core\Sales\ValueObject\Money;
use JD\DDD\Core\Sales\ValueObject\OrderId;
use JD\DDD\Core\Sales\ValueObject\ProductId;
use JD\DDD\Core\Sales\ValueObject\ProductName;
use JD\DDD\Core\Sales\ValueObject\ProductQuantity;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private const VALID_ID = '906416e9-4849-42d6-a226-931ce10c4be3';
    private const VALID_ID_2 = '906416e9-4849-42d6-a226-931ce10c4be4';
    private const VALID_ID_3 = '906416e9-4849-42d6-a226-931ce10c4be5';
    private const PRODUCT_NAME = 'Bicycle';
    private const PRODUCT_QTY = 7;
    public function testOrderWithInvalidOrderItemQty(): void
    {
        $this->expectExceptionMessage('There is no such amount of product in stock');
        $orderId = OrderId::fromString(self::VALID_ID);
        $customerId = CustomerId::fromString(self::VALID_ID);


        $product = $this->constructProduct(
            200,
            Currency::fromIsoCode(Currency::CURRENCY_USD)
        );

        $order = Order::create($orderId, $customerId);
        $order->addOrderProductItem($product, ProductQuantity::fromQty(9));
    }

    public function testInvalidOrderItemMaxQty(): void
    {
        $this->expectExceptionMessage(
            \sprintf(
                'Order max items \'%s\' number exceeded',
                Order::MAX_ITEMS_IN_ORDER
            )
        );
        $orderId = OrderId::fromString(self::VALID_ID);
        $customerId = CustomerId::fromString(self::VALID_ID);

        $product1 = $this->constructProduct(
            200,
            Currency::fromIsoCode(Currency::CURRENCY_USD)
        );

        $product2 = $this->constructProduct(
            200,
            Currency::fromIsoCode(Currency::CURRENCY_USD),
            self::VALID_ID_2
        );

        $product3 = $this->constructProduct(
            200,
            Currency::fromIsoCode(Currency::CURRENCY_USD),
            self::VALID_ID_3
        );

        $order = Order::create($orderId, $customerId);

        foreach ([$product1, $product2, $product3] as $product) {
            $order->addOrderProductItem($product, ProductQuantity::fromQty(self::PRODUCT_QTY));
        }
    }

    public function testInvalidOrderItemCurrency(): void
    {
        $this->expectExceptionMessage('Order should not contain items in different currency prices');
        $orderId = OrderId::fromString(self::VALID_ID);
        $customerId = CustomerId::fromString(self::VALID_ID);

        $product1 = $this->constructProduct(
            200,
            Currency::fromIsoCode(Currency::CURRENCY_USD)
        );

        $product2 = $this->constructProduct(
            200,
            Currency::fromIsoCode(Currency::CURRENCY_EUR),
            self::VALID_ID_2
        );


        $order = Order::create($orderId, $customerId);

        $order->addOrderProductItem($product1, ProductQuantity::fromQty(1));
        $order->addOrderProductItem($product2, ProductQuantity::fromQty(1));
    }

    public function testCorrectOrderItems(): void
    {
        $orderId = OrderId::fromString(self::VALID_ID);
        $customerId = CustomerId::fromString(self::VALID_ID);
        $productPrice1 = 300;
        $productPrice2 = 500;

        $productQty1 = 5;
        $productQty2 = 3;

        $subTotals = ($productQty1*$productPrice1) + ($productQty2 * $productPrice2);
        $totals = (int) \ceil($subTotals + ($subTotals * Order::ORDER_TAX_PERCENTAGE));

        $product1 = $this->constructProduct(
            $productPrice1,
            Currency::fromIsoCode(Currency::CURRENCY_USD)
        );

        $product2 = $this->constructProduct(
            $productPrice2,
            Currency::fromIsoCode(Currency::CURRENCY_USD),
            self::VALID_ID_2
        );


        $order = Order::create($orderId, $customerId);

        $order->addOrderProductItem($product1, ProductQuantity::fromQty($productQty1));
        $order->addOrderProductItem($product2, ProductQuantity::fromQty($productQty2));

        $this->assertCount(2, $order->getOrderProductItems());
        $this->assertSame(self::VALID_ID, $order->getOrderId()->getId());
        $this->assertSame(self::VALID_ID, $order->getCustomerId()->getId());

        $this->assertSame($subTotals, $order->getSubTotals()->getAmount());
        $this->assertSame($totals, $order->getTotals()->getAmount());
    }

    public function testRemoveOrderItems(): void
    {
        $orderId = OrderId::fromString(self::VALID_ID);
        $customerId = CustomerId::fromString(self::VALID_ID);
        $productPrice1 = 300;
        $productPrice2 = 500;

        $productQty1 = 5;
        $productQty2 = 3;

        $subTotals1 = ($productQty1*$productPrice1) + ($productQty2 * $productPrice2);
        $totals1 = (int) \ceil($subTotals1 + ($subTotals1 * Order::ORDER_TAX_PERCENTAGE));

        $subTotals2 = $productQty2 * $productPrice2;
        $totals2 = (int) \ceil($subTotals2 + ($subTotals2 * Order::ORDER_TAX_PERCENTAGE));

        $product1 = $this->constructProduct(
            $productPrice1,
            Currency::fromIsoCode(Currency::CURRENCY_USD)
        );

        $product2 = $this->constructProduct(
            $productPrice2,
            Currency::fromIsoCode(Currency::CURRENCY_USD),
            self::VALID_ID_2
        );


        $order = Order::create($orderId, $customerId);

        $order->addOrderProductItem($product1, ProductQuantity::fromQty($productQty1));
        $order->addOrderProductItem($product2, ProductQuantity::fromQty($productQty2));

        $this->assertCount(2, $order->getOrderProductItems());

        $this->assertSame($subTotals1, $order->getSubTotals()->getAmount());
        $this->assertSame($totals1, $order->getTotals()->getAmount());

        $order->removeOrderItem($product2->getProductId());

        $this->assertSame($subTotals2, $order->getSubTotals()->getAmount());
        $this->assertSame($totals2, $order->getTotals()->getAmount());
    }

    private function constructProduct(
        int $productPrice,
        Currency $productCurrency,
        string $id = self::VALID_ID
    ): Product {
        $productId = ProductId::fromString($id);
        $productName = ProductName::fromString(self::PRODUCT_NAME);
        $productPrice = Money::fromAmountAndCurrency(
            $productPrice,
            $productCurrency
        );

        $productQuantity = ProductQuantity::fromQty(self::PRODUCT_QTY);

        return Product::create(
            $productId,
            $productName,
            $productPrice,
            $productQuantity,
        );
    }
}
