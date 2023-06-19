<?php

declare(strict_types=1);

namespace JD\DDD\ValueObject;

interface ComparableInterface
{
    public function equals(object $comparable): bool;
}
