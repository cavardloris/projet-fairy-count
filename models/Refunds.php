<?php

class Refund
{
    public function __construct( private int $debtorId, private int $creditorId, private float $amount, private ?int $id = null, private ?string $createdAt = null ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDebtorId(): int
    {
        return $this->debtorId;
    }

    public function setDebtorId(int $debtorId): void
    {
        $this->debtorId = $debtorId;
    }

    public function getCreditorId(): int
    {
        return $this->creditorId;
    }

    public function setCreditorId(int $creditorId): void
    {
        $this->creditorId = $creditorId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

}