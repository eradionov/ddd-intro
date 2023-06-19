<?php

declare(strict_types=1);

namespace ValueObject;

use JD\DDD\ValueObject\Currency;
use JD\DDD\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    private const CURRENCY_USD = 'USD';
    private const CURRENCY_EUR = 'EUR';
    private const ERROR_MESSAGE_EXP = '\'%s\' is not a valid ISO code';

    #[DataProvider('currencyInvalidIsoCode')]
    public function testInvalidCurrencyCode(string $isoCode): void
    {
        $this->expectExceptionMessage(\sprintf(self::ERROR_MESSAGE_EXP, $isoCode));
        $this->expectException(\InvalidArgumentException::class);

        new Currency($isoCode);
    }

    #[DataProvider('currencyCorrectIsoCode')]
    public function testCorrectCurrencyCode(string $isoCode): void
    {
        $currency = new Currency($isoCode);

        $this->assertEquals($isoCode, $currency->getIsoCode());
    }

    public function testSameCurrencyCode(): void
    {
        $currency = new Currency(self::CURRENCY_USD);
        $currencySame = new Currency(self::CURRENCY_USD);

        $this->assertNotSame($currency, $currencySame);
        $this->assertTrue($currency->equals($currencySame));
    }

    public static function currencyInvalidIsoCode(): array
    {
        return [
            [''],
            ['CAD'],
            ['RUB'],
            ['BTC'],
        ];
    }

    public static function currencyCorrectIsoCode(): array
    {
        return [
            [self::CURRENCY_USD],
            [self::CURRENCY_EUR],
        ];
    }
}