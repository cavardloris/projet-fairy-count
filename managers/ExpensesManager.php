<?php

class ExpenseManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT expenses.*, users.id as id_users, email, users.name as user_name, password, categories.id as id_category, categories.name as category_name FROM categories JOIN expenses ON categories.id_category = expenses.category_id JOIN users ON expenses.user_id = users.id_users');
        $parameters = [

        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenses = [];

        foreach($result as $item)
        {
            $category = new Category($item['category_name'], $item['id_category']);
            $user = new User($item["user_name"], $item["email"], $item["password"], $item["user_id"]);
            $expense = new Expense($user, $item["amount"], $category, $item["id"]);
            $expenses[] = $expense;
        }

        return $expenses;
    }

    public function findById(int $id) : ? Expense
    {
        $query = $this->db->prepare('SELECT expenses.*, users.id as id_users, email, users.name as user_name, password, categories.id as id_category, categories.name as category_name FROM categories JOIN expenses ON categories.id_category = expenses.category_id JOIN users ON expenses.user_id = users.id_users WHERE expenses.id = :id');
        $parameters = [
            "id" => $id
        ];
        $query->execute($parameters);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if($item)
        {
            $category = new Category($item['category_name'], $item['id_category']);
            $user = new User($item["user_name"], $item["email"], $item["password"], $item["user_id"]);
            return new Expense($user, $item["amount"], $category, $item["id"]);
        }

        return null;
    }
}