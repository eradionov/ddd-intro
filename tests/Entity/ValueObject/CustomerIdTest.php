<?php

declare(strict_types=1);

namespace Entity\ValueObject;

use JD\DDD\Entity\ValueObject\CustomerId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomerIdTest extends TestCase
{
    private const VALID_UUID = '906416e9-4849-42d6-a226-931ce10c4be3';

    #[DataProvider('invalidCustomerUid')]
    public function testInvalidCustomerUid(string $id): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CustomerId::fromString($id);
    }

    public function testValidUid(): void
    {
        $customerId = CustomerId::fromString(self::VALID_UUID);

        $this->assertEquals(self::VALID_UUID, $customerId->getId());
    }

    public function testEqualUid(): void
    {
        $customerUid = CustomerId::fromString(self::VALID_UUID);
        $customerUid2 = CustomerId::fromString(self::VALID_UUID);

        $this->assertNotSame($customerUid, $customerUid2);
        $this->assertTrue($customerUid->equals($customerUid2));
    }

    public static function invalidCustomerUid(): array
    {
        return [
            [''],
            ['1'],
            ['test'],
        ];
    }
}