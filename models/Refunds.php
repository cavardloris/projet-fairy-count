<?php

class Refund
{
    public function __construct( private User $debtor, private User $creditor,  private float $amount, private ?int $id = null ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDebtor(): User
    {
        return $this->debtor;
    }

    public function setDebtor(User $debtor): void
    {
        $this->debtor = $debtor;
    }

    public function getCreditor(): User
    {
        return $this->creditor;
    }

    public function setCreditor(User $creditor): void
    {
        $this->creditor = $creditor;
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