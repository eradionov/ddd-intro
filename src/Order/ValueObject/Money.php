<?php

declare(strict_types=1);

namespace JD\DDD\Order\ValueObject;

use Assert\Assertion;
use JD\DDD\Common\ComparableInterface;

final class Money implements ComparableInterface
{
    private int $amount;
    private Currency $currency;

    public static function fromCurrency(Currency $currency): self
    {
        return new self(0, $currency);
    }

    public static function fromAmountAndCurrency(int $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    private function __construct(int $amount, Currency $currency) {
        Assertion::min($amount, 0, 'Money amount can\'t be negative');

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function increaseAmount(int $amountToIncrease): self
    {
        return new self(
            $this->getAmount() + $amountToIncrease,
            $this->currency
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function equals(object $comparable): bool
    {
        return $comparable instanceof self
            && $comparable->getCurrency()->equals($this->currency)
            && $comparable->getAmount() === $this->amount;
    }
}
