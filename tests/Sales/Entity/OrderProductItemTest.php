<?php

declare(strict_types=1);

namespace Sales\Entity;

use JD\DDD\Core\Sales\Entity\OrderProductItem;
use JD\DDD\Core\Sales\ValueObject\Currency;
use JD\DDD\Core\Sales\ValueObject\Money;
use JD\DDD\Core\Sales\ValueObject\ProductId;
use JD\DDD\Core\Sales\ValueObject\ProductQuantity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderProductItemTest extends TestCase
{
    private const VALID_ID = '906416e9-4849-42d6-a226-931ce10c4be3';
    private const ERROR_QUANTITY_MESSAGE = 'Quantity should be in range \'%d\' to \'%d\'';

    #[DataProvider('invalidOrderLineQuantity')]
    public function testInvalidLineQuantity(int $qty): void
    {
        $this->expectExceptionMessage(
            \sprintf(
                self::ERROR_QUANTITY_MESSAGE,
                OrderProductItem::MIN_PRODUCT_COUNT,
                OrderProductItem::MAX_PRODUCT_COUNT
            )
        );

        OrderProductItem::create(
            ProductId::fromString(self::VALID_ID),
            Money::fromCurrency(Currency::fromIsoCode(Currency::CURRENCY_USD)),
            ProductQuantity::fromQty($qty)
        );

    }

    public function testValidLine(): void
    {
        $qty = 10;
        $price = 200;
        $productId = ProductId::fromString(self::VALID_ID);
        $productPrice = Money::fromAmountAndCurrency($price, Currency::fromIsoCode(Currency::CURRENCY_USD));
        $lineQty = ProductQuantity::fromQty($qty);

        $orderItem = OrderProductItem::create(
            $productId,
            $productPrice,
            $lineQty
        );

        $this->assertSame($price * $qty, $orderItem->getLinePrice()->getAmount());
        $this->assertSame($price, $orderItem->getProductPrice()->getAmount());

        $this->assertSame($productId, $orderItem->getProductId());
        $this->assertSame($productPrice, $orderItem->getProductPrice());
        $this->assertSame($lineQty, $orderItem->getQty());
    }

    public function testChangeLineQuantity(): void
    {
        $qty = 10;
        $changedQty = 7;
        $price = 200;

        $orderItem = OrderProductItem::create(
            ProductId::fromString(self::VALID_ID),
            Money::fromAmountAndCurrency($price, Currency::fromIsoCode(Currency::CURRENCY_USD)),
            ProductQuantity::fromQty($qty)
        );

        $this->assertSame($price * $qty, $orderItem->getLinePrice()->getAmount());
        $this->assertSame($price, $orderItem->getProductPrice()->getAmount());

        $changedQtyItem = $orderItem->changeQuantity(ProductQuantity::fromQty($changedQty));

        $this->assertSame($price * $changedQty, $changedQtyItem->getLinePrice()->getAmount());
        $this->assertSame($price, $changedQtyItem->getProductPrice()->getAmount());
        $this->assertNotSame($orderItem, $changedQtyItem);
    }

    public static function invalidOrderLineQuantity(): array
    {
        return [
            [-1],
            [0],
            [11],
        ];
    }
}
