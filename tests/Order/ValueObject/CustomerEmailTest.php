<?php

declare(strict_types=1);

namespace Order\ValueObject;

use JD\DDD\Order\ValueObject\CustomerEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomerEmailTest extends TestCase
{
    private const ERROR_MESSAGE = '\'%s\' email is invalid';
    private const VALID_EMAIL = 'test.email@google.me';

    #[DataProvider('invalidEmailData')]
    public function testInvalidEmails(string $email): void
    {
        $this->expectExceptionMessage(\sprintf(self::ERROR_MESSAGE, $email));
        $this->expectException(\InvalidArgumentException::class);

        CustomerEmail::fromString($email);
    }

    public function testValidEmail(): void
    {
        $customerEmail = CustomerEmail::fromString(self::VALID_EMAIL);

        $this->assertEquals(self::VALID_EMAIL, $customerEmail->getEmail());
    }

    public function testEqualEmails(): void
    {
        $customerEmail = CustomerEmail::fromString(self::VALID_EMAIL);
        $customerEmail2 = CustomerEmail::fromString(self::VALID_EMAIL);

        $this->assertNotSame($customerEmail, $customerEmail2);
        $this->assertTrue($customerEmail->equals($customerEmail2));
    }

    public static function invalidEmailData(): array
    {
        return [
            [''],
            ['http://test.com'],
            ['test'],
            ['0'],
        ];
    }
}
