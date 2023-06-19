<?php

declare(strict_types=1);

namespace JD\DDD\ValueObject;

final class Currency implements ComparableInterface
{
    private string $isoCode;

    public function __construct(string $isoCode)
    {
        // TODO: it's just a simple example
        if (!preg_match('/USD|EUR$/', $isoCode)) {
            throw new \InvalidArgumentException(
                sprintf('\'%s\' is not a valid ISO code', $isoCode)
            );
        }

        $this->isoCode = $isoCode;
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
