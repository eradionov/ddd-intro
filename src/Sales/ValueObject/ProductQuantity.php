<?php

declare(strict_types=1);

namespace JD\DDD\Sales\ValueObject;

use Assert\Assertion;
use JD\DDD\Common\ComparableInterface;

final class ProductQuantity implements ComparableInterface
{
    private const MIN_QTY = 1;
    private const MAX_QTY = 10;
    private int $qty;

    private function __construct(int $qty)
    {
        Assertion::between($qty, self::MIN_QTY, self::MAX_QTY,
            \sprintf('Quantity should be in range \'%d\' to \'%d\'', self::MIN_QTY, self::MAX_QTY)
        );

        $this->qty = $qty;
    }


    public static function fromQty(int $qty): self
    {
        return new self($qty);
    }

    public function increaseQty(int $qty): self
    {
        return new self(
            $this->getQty() + $qty
        );
    }

    public function decreaseQty(int $qty): self
    {
        return new self(
            $this->getQty() - $qty
        );
    }

    public function equals(object $comparable): bool
    {
        return $comparable instanceof self && $comparable->getQty() === $this->qty;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
}
