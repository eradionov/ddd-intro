<?php

declare(strict_types=1);

namespace Order\ValueObject;

use JD\DDD\Order\ValueObject\Currency;
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

        Currency::fromIsoCode($isoCode);
    }

    #[DataProvider('currencyCorrectIsoCode')]
    public function testCorrectCurrencyCode(string $isoCode): void
    {
        $currency = Currency::fromIsoCode($isoCode);

        $this->assertEquals($isoCode, $currency->getIsoCode());
    }

    public function testSameCurrencyCode(): void
    {
        $currency = Currency::fromIsoCode(self::CURRENCY_USD);
        $currencySame = Currency::fromIsoCode(self::CURRENCY_USD);

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