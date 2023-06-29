<?php

declare(strict_types=1);

namespace JD\DDD\Core\Sales\ValueObject;

use Assert\Assertion;

final class ProductDescription
{
    private const MIN_CHARACTER_LENGTH = 20;
    private const MAX_CHARACTER_LENGTH = 255;

    private string $description;

    private function __construct(string $description)
    {
        $description = \trim($description);

        Assertion::notBlank($description);
        Assertion::betweenLength($description, self::MIN_CHARACTER_LENGTH, self::MAX_CHARACTER_LENGTH);

        $this->description = $description;
    }

    public static function fromString(string $description = ''): self
    {
        return new self($description);
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
