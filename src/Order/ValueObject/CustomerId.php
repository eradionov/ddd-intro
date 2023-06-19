<?php

declare(strict_types=1);

namespace JD\DDD\Order\ValueObject;

use Assert\Assertion;
use JD\DDD\Common\ComparableInterface;

final class CustomerId implements ComparableInterface
{
    private string $id;

    private function __construct(string $id)
    {
        Assertion::uuid($id, 'Customer Id is not valid');

        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public static function fromString(string $customerId): self
    {
        return new self($customerId);
    }

    public function equals(object $comparable): bool
    {
        return $comparable instanceof self && $this->id === $comparable->getId();
    }
}
