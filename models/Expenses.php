<?php

class Expense
{
    public function __construct( private User $user, private float $amount, private int $categorieId, private ?int $id = null ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCategorieId(): int
    {
        return $this->categorieId;
    }

    public function setCategorieId(int $categorieId): void
    {
        $this->categorieId = $categorieId;
    }
}