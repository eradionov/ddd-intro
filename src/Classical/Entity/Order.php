<?php

declare(strict_types=1);

namespace JD\DDD\Classical\Entity;

use JD\DDD\Classical\Vendor\ArrayCollection;
use JD\DDD\Classical\Vendor\Collection;

class Order
{
    private int $id;
    private string $orderId;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    /** OrderProductItem[] */
    #[OneToMany(targetEntity: OrderProductItem::class, mappedBy: 'order')]
    private Collection $orderItems;
    private int $subTotals;
    private int $totalPrice;

    public function __construct()
    {
        $this->orderId = \sha1((string) \mt_rand());
        $this->orderItems = new ArrayCollection();
        $this->subTotals = 0;
        $this->totalPrice = 0;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function addOrderItems(OrderProductItem $orderItem): void
    {
        if ($this->orderItems->contains($orderItem)) {
            return;
        }

        $this->orderItems->add($orderItem);
        $this->recalculateTotalPrice();
        $this->recalculateSubTotals();
    }

    public function removeOrderItems(OrderProductItem $orderItem): void
    {
        if (!$this->orderItems->contains($orderItem)) {
            return;
        }

        $this->orderItems->removeElement($orderItem);
        $this->recalculateTotalPrice();
        $this->recalculateSubTotals();
    }

    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function getSubTotals(): int
    {
        return $this->subTotals;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    private function recalculateSubTotals(): void
    {
        // Do some recalculation
    }
    private function recalculateTotalPrice(): void
    {
        // Do some recalculation
    }
}
