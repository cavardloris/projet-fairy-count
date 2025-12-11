<?php

class ExpenseManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT expenses.*, users.id as user_id, email, name, password FROM expenses JOIN users ON expenses.user_id = users.id');
        $parameters = [

        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenses = [];

        foreach($result as $item)
        {
            $user = new User($item["name"], $item["email"], $item["password"], $item["user_id"]);
            $expense = new Expense($user, $item["amount"], $item["categorieId"], $item["id"]);
            $expenses[] = $expense;
        }

        return $expenses;
    }
}