<?php

declare(strict_types=1);

namespace JD\DDD\Classical\Vendor;

abstract class Collection
{
    public function contains(object $element): bool
    {
        // ....

        return false;
    }

    public function removeElement(object $element): void
    {
        // ....
    }

    public function add(object $element): void
    {
        // ....
    }
}
