<?php

declare(strict_types=1);

namespace JD\DDD\ValueObject;

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

    public function __construct(int $amount, Currency $currency) {
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function increaseAmount(int $amountToIncrease): self
    {
        return new self(
            $this->getAmount() + $amountToIncrease,
            $this->currency
        );
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

    private function setAmount(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Money amount can\'t be negative');
        }

        $this->amount = $amount;
    }

    private function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }
}
