<?php

class ExpenseManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT * FROM expenses');
        $parameters = [

        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenses = [];

        foreach($result as $item)
        {
            $expense = new Expense($item["name"], $item["email"], $item["password"], $item["id"]);
            $users[] = $user;
        }

        return $users;
    }
}