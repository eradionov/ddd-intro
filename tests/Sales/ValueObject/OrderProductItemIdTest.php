<?php

declare(strict_types=1);

namespace Sales\ValueObject;

use JD\DDD\Core\Sales\ValueObject\CustomerId;
use JD\DDD\Core\Sales\ValueObject\OrderProductItemId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderProductItemIdTest extends TestCase
{
    private const VALID_UUID = '906416e9-4849-42d6-a226-931ce10c4be3';

    #[DataProvider('invalidOrderProductItemUid')]
    public function testInvalidCustomerUid(string $id): void
    {
        $this->expectException(\InvalidArgumentException::class);

        OrderProductItemId::fromString($id);
    }

    public function testValidUid(): void
    {
        $customerId = OrderProductItemId::fromString(self::VALID_UUID);

        $this->assertEquals(self::VALID_UUID, $customerId->getId());
    }

    public function testEqualUid(): void
    {
        $customerUid = OrderProductItemId::fromString(self::VALID_UUID);
        $customerUid2 = OrderProductItemId::fromString(self::VALID_UUID);

        $this->assertNotSame($customerUid, $customerUid2);
        $this->assertTrue($customerUid->equals($customerUid2));
    }

    public function testNotEqualUid(): void
    {
        $customerUid = CustomerId::fromString(self::VALID_UUID);
        $orderId = OrderProductItemId::fromString(self::VALID_UUID);

        $this->assertNotSame($customerUid, $orderId);
        $this->assertFalse($customerUid->equals($orderId));
    }

    public static function invalidOrderProductItemUid(): array
    {
        return [
            [''],
            ['1'],
            ['test'],
        ];
    }
}
