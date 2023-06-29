<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\ValueObject;

use Assert\Assertion;

final class ProductName
{
    private const MIN_CHARACTER_LENGTH = 5;
    private const MAX_CHARACTER_LENGTH = 120;


    private string $name;

    private function __construct(string $name)
    {
        $name = \trim($name);
        Assertion::notBlank($name);
        Assertion::betweenLength($name, self::MIN_CHARACTER_LENGTH, self::MAX_CHARACTER_LENGTH);

        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
