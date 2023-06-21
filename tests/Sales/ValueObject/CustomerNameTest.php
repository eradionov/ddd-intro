<?php

declare(strict_types=1);

namespace Sales\ValueObject;

use JD\DDD\Sales\ValueObject\CustomerName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomerNameTest extends TestCase
{
    private const VALID_FIRSTNAME = 'john';
    private const VALID_LASTNAME = 'dow';

    #[DataProvider('invalidCustomerName')]
    public function testInvalidCustomerName(string $firstName, string $lastName): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CustomerName::fromFistAndLastName($firstName, $lastName);
    }

    public function testValidCustomerName(): void
    {
        $customerName = CustomerName::fromFistAndLastName(self::VALID_FIRSTNAME, self::VALID_LASTNAME);

        $expectedFirstName = \ucfirst(self::VALID_FIRSTNAME);
        $expectedLastName = \ucfirst(self::VALID_LASTNAME);

        $this->assertEquals($expectedFirstName, $customerName->getFirstName());
        $this->assertEquals($expectedLastName, $customerName->getLastName());

        $this->assertEquals(
            \sprintf('%s %s', $expectedFirstName, $expectedLastName),
            $customerName->getFullName()
        );
    }

    #[DataProvider('invalidCustomerNameLength')]
    public function testInvalidCustomerNameLength(string $firstName, string $lastName): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CustomerName::fromFistAndLastName($firstName, $lastName);
    }

    public static function invalidCustomerNameLength(): array
    {
        return [
            ['W', ''],
            ['Ji', 'W'],
            ['wertyopadoftrmkrtopdp', 'Wu'],
            ['Wu', 'wertyopadoftrmkrtopdp'],
        ];
    }

    public static function invalidCustomerName(): array
    {
        return [
            ['', ''],
            ['test', ''],
            ['Test', '   '],
            ['   ', 'Test'],
            ['0', '12'],
            ['.', '.'],
        ];
    }
}
