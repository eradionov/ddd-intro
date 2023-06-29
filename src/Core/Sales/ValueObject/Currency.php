<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\ValueObject;

use JD\DDD\Common\ComparableInterface;

final class Currency implements ComparableInterface
{
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_EUR = 'EUR';

    private string $isoCode;

    private function __construct(string $isoCode)
    {
        // TODO: it's just a simple example, we do not need complicate with different iso codes
        if (\in_array(\preg_match('/USD|EUR$/', $isoCode), [0, false], true)) {
            throw new \InvalidArgumentException(
                \sprintf('\'%s\' is not a valid ISO code', $isoCode)
            );
        }

        $this->isoCode = $isoCode;
    }

    public static function fromIsoCode(string $isoCode): self
    {
        return new self($isoCode);
    }

    public static function fromUSD(): self
    {
        return new self(self::CURRENCY_USD);
    }

    public static function fromEUR(): self
    {
        return new self(self::CURRENCY_EUR);
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function equals(object $comparable): bool
    {
        return $comparable instanceof self
            && $comparable->getIsoCode() === $this->isoCode;
    }
}
