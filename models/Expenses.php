<?php
namespace Models;

class Expense {
    private ?int $id = null;
    private int $userId;
    private float $amount;
    private int $categorieId;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function setAmount(float $amount): void {
        $this->amount = $amount;
    }

    public function getCategorieId(): int {
        return $this->categorieId;
    }

    public function setCategorieId(int $categorieId): void {
        $this->categorieId = $categorieId;
    }
}
?>