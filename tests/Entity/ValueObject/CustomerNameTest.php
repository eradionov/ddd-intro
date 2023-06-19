<?php

declare(strict_types=1);

namespace Entity\ValueObject;

use JD\DDD\Entity\ValueObject\CustomerName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomerNameTest extends TestCase
{
    private const VALID_FIRSTNAME = 'John';
    private const VALID_LASTNAME = 'Dow';

    #[DataProvider('invalidCustomerName')]
    public function testInvalidCustomerName(string $firstName, string $lastName): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CustomerName::fromFistAndLastName($firstName, $lastName);
    }

    public function testValidCustomerName(): void
    {
        $customerName = CustomerName::fromFistAndLastName(self::VALID_FIRSTNAME, self::VALID_LASTNAME);

        $this->assertEquals(self::VALID_FIRSTNAME, $customerName->getFirstName());
        $this->assertEquals(self::VALID_LASTNAME, $customerName->getLastName());

        $this->assertEquals(
            \sprintf('%s %s', self::VALID_FIRSTNAME, self::VALID_LASTNAME),
            $customerName->getFullName());
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