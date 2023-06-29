<?php

declare(strict_types=1);

namespace Sales\ValueObject;

use JD\DDD\Core\Sales\ValueObject\Currency;
use JD\DDD\Core\Sales\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    private const CURRENCY = 'USD';
    private const ERROR_MESSAGE = 'Money amount can\'t be negative';

    #[DataProvider('amountWithCurrencyNegative')]
    public function testFromAmountAndCurrencyNegative(int $amount, Currency $currency): void
    {
        $this->expectExceptionMessage(self::ERROR_MESSAGE);
        $this->expectException(\InvalidArgumentException::class);

        Money::fromAmountAndCurrency($amount, $currency);
    }

    #[DataProvider('amountWithCurrencyPositive')]
    public function testFromAmountAndCurrencyPositive(int $amount, Currency $currency): void
    {
        $money = Money::fromAmountAndCurrency($amount, $currency);

        $this->assertTrue($money->getCurrency()->equals($currency));
        $this->assertEquals($amount, $money->getAmount());
    }

    public function testChangeAmount(): void
    {
        $money = Money::fromAmountAndCurrency(100, Currency::fromIsoCode(self::CURRENCY));
        $changedMoney = $money->increaseAmount(200);

        $this->assertNotSame($money, $changedMoney);
        $this->assertFalse($money->equals($changedMoney));

        $this->assertEquals(300, $changedMoney->getAmount());
        $this->assertEquals(100, $money->getAmount());
    }

    public function testCreateFromCurrency(): void
    {
        $money = Money::fromCurrency(Currency::fromIsoCode(self::CURRENCY));

        $this->assertEquals(0, $money->getAmount());
        $this->assertEquals(self::CURRENCY, $money->getCurrency()->getIsoCode());
    }

    public function testSameMoney(): void
    {
        $money = Money::fromCurrency(Currency::fromIsoCode(self::CURRENCY));
        $moneySame = Money::fromCurrency(Currency::fromIsoCode(self::CURRENCY));

        $this->assertNotSame($moneySame, $money);
        $this->assertTrue($money->equals($moneySame));
    }

    public static function amountWithCurrencyNegative(): array
    {
        return [
            [-1, Currency::fromIsoCode(self::CURRENCY)],
        ];
    }

    public static function amountWithCurrencyPositive(): array
    {
        return [
            [100, Currency::fromIsoCode(self::CURRENCY)],
            [0, Currency::fromIsoCode(self::CURRENCY)],
        ];
    }
}
