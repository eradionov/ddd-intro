<?php

declare(strict_types=1);

namespace JD\DDD\Sales\ValueObject;

use Assert\Assertion;

final class CustomerName
{
    private const MIN_CHARACTER_LENGTH = 2;
    private const MAX_CHARACTER_LENGTH = 20;
    private const VALID_NAME_FORMAT = '/[a-zA-Z]+/';
    private string $firstName;
    private string $lastName;

    private function __construct(string $firstName, string $lastName)
    {
        $firstName = \trim($firstName);
        $lastName = \trim($lastName);

        Assertion::notBlank($firstName);
        Assertion::betweenLength($firstName, self::MIN_CHARACTER_LENGTH, self::MAX_CHARACTER_LENGTH);
        Assertion::regex($firstName, self::VALID_NAME_FORMAT);

        Assertion::notBlank($lastName);
        Assertion::betweenLength($lastName, self::MIN_CHARACTER_LENGTH, self::MAX_CHARACTER_LENGTH);
        Assertion::regex($lastName, self::VALID_NAME_FORMAT);

        $this->firstName = \ucfirst($firstName);
        $this->lastName = \ucfirst($lastName);
    }

    public static function fromFistAndLastName(string $firstName, string $lastName): self
    {
        return new self($firstName, $lastName);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return \sprintf('%s %s', $this->firstName, $this->lastName);
    }
}
