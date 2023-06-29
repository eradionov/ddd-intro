<?php

declare(strict_types=1);

namespace Sales\ValueObject;

use JD\DDD\Core\Sales\ValueObject\OrderId;
use JD\DDD\Core\Sales\ValueObject\ProductId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProductIdTest extends TestCase
{
    protected const VALID_UUID = '906416e9-4849-42d6-a226-931ce10c4be3';

    #[DataProvider('getInvalidUid')]
    public function testInvalidProductUid(string $id): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ProductId::fromString($id);
    }

    public function testValidUid(): void
    {
        $customerId = ProductId::fromString(self::VALID_UUID);

        $this->assertEquals(self::VALID_UUID, $customerId->getId());
    }

    public function testEqualUid(): void
    {
        $productUid = ProductId::fromString(self::VALID_UUID);
        $productUid2 = ProductId::fromString(self::VALID_UUID);

        $this->assertNotSame($productUid, $productUid2);
        $this->assertTrue($productUid->equals($productUid2));
    }

    public function testNotEqualUid(): void
    {
        $productUid = ProductId::fromString(self::VALID_UUID);
        $orderId = OrderId::fromString(self::VALID_UUID);

        $this->assertNotSame($productUid, $orderId);
        $this->assertFalse($productUid->equals($orderId));
    }

    public static function getInvalidUid(): array
    {
        return [
            [''],
            ['1'],
            ['test'],
        ];
    }
}
