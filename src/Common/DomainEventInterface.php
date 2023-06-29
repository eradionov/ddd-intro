<?php

declare(strict_types=1);

namespace JD\DDD\Common;

interface DomainEventInterface
{
    public function getId(): string;
}
