<?php

class ExpenseShare
{
    public function __construct( private int $expensesId, private int $usersId, private float $shareAmounts ) {}

    public function getExpensesId(): int
    {
        return $this->expensesId;
    }

    public function setExpensesId(int $expensesId): void
    {
        $this->expensesId = $expensesId;
    }

    public function getUsersId(): int
    {
        return $this->usersId;
    }

    public function setUsersId(int $usersId): void
    {
        $this->usersId = $usersId;
    }

    public function getShareAmounts(): float
    {
        return $this->shareAmounts;
    }

    public function setShareAmounts(float $shareAmounts): void
    {
        $this->shareAmounts = $shareAmounts;
    }
}