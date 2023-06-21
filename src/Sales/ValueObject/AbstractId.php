<?php

declare(strict_types=1);

namespace JD\DDD\Sales\ValueObject;

use Assert\Assertion;
use JD\DDD\Common\ComparableInterface;

abstract class AbstractId implements ComparableInterface
{
    private string $id;

    final private function __construct(string $id)
    {
        Assertion::uuid($id, 'Id is not valid');

        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public static function fromString(string $orderId): static
    {
        return new static($orderId);
    }

    public function equals(object $comparable): bool
    {
        return $comparable instanceof static && $this->id === $comparable->getId();
    }
}
