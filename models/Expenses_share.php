<?php

class ExpenseShare
{
    public function __construct( private Expense $expense, private User $user, private float $shareAmounts ) {}

    public function getExpense(): Expense
    {
        return $this->expense;
    }

    public function setExpense(Expense $expense): void
    {
        $this->expense = $expense;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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