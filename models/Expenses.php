<?php

class Expense
{
    public function __construct( private int $userId, private float $amount, private int $categorieId, private ?int $id = null ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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