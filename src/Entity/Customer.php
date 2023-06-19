<?php

declare(strict_types=1);

namespace JD\DDD\Entity;

use JD\DDD\Entity\ValueObject\CustomerEmail;
use JD\DDD\Entity\ValueObject\CustomerId;
use JD\DDD\Entity\ValueObject\CustomerName;

final class Customer
{
    public function __construct(
        public readonly CustomerId $customerId,
        private CustomerName $customerName,
        private CustomerEmail $customerEmail,
    ) {
    }

    public function changeName(CustomerName $amendedName): void
    {
        $this->customerName = $amendedName;
    }

    public function changeEmail(CustomerEmail $amendedEmail): void
    {
        $this->customerEmail = $amendedEmail;
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getCustomerName(): CustomerName
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): CustomerEmail
    {
        return $this->customerEmail;
    }
}