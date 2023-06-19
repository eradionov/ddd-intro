<?php

declare(strict_types=1);

namespace JD\DDD\Order\ValueObject;

use Assert\Assertion;
use JD\DDD\Common\ComparableInterface;

final class CustomerEmail implements ComparableInterface
{
    private string $email;

    private function __construct(string $email)
    {
        Assertion::email($email, \sprintf('\'%s\' email is invalid', $email));

        $this->email = $email;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function equals(object $comparable): bool
    {
        return $comparable instanceof self
            && $comparable->getEmail() === $this->email;
    }
}
