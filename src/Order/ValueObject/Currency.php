<?php

declare(strict_types=1);

namespace JD\DDD\Order\ValueObject;

use JD\DDD\Common\ComparableInterface;

final class Currency implements ComparableInterface
{
    private string $isoCode;

    private function __construct(string $isoCode)
    {
        // TODO: it's just a simple example, we do not need complicate with different iso codes
        if (false === \preg_match('/USD|EUR$/', $isoCode)) {
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
