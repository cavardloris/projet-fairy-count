<?php

class ExpenseManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT expenses.*, users.id as id_users, email, users.name as user_name, password, categories.id as id_category, categories.name as category_name FROM categories JOIN expenses ON categories.id = expenses.category_id JOIN users ON expenses.user_id = users.id');
        $parameters = [

        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenses = [];

        foreach($result as $item)
        {
            $category = new Category($item['category_name'], $item['id_category']);
            $user = new User($item["user_name"], $item["email"], $item["password"], $item["id_users"]);
            $expense = new Expense($user, $item["amount"], $category, $item["id"]);
            $expenses[] = $expense;
        }

        return $expenses;
    }

    public function findById(int $id) : ? Expense
    {
        $query = $this->db->prepare('SELECT expenses.*, users.id as id_users, email, users.name as user_name, password, categories.id as id_category, categories.name as category_name FROM categories JOIN expenses ON categories.id = expenses.category_id JOIN users ON expenses.user_id = users.id WHERE expenses.id = :id');
        $parameters = [
            "id" => $id
        ];
        $query->execute($parameters);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if($item)
        {
            $category = new Category($item['category_name'], $item['id_category']);
            $user = new User($item["user_name"], $item["email"], $item["password"], $item["id_users"]);
            return new Expense($user, $item["amount"], $category, $item["id"]);
        }

        return null;
    }

    public function create(Expense $expense) : ?int
    {
        $query = $this->db->prepare('INSERT INTO expenses (user_id, amount, category_id) VALUES (:user_id, :amount, :category_id)');
        $parameters = [
            "user_id" => $expense->getUser()->getId(),
            "amount" => $expense->getAmount(),
            "category_id" => $expense->getCategorie()->getId()
        ];
        $query->execute($parameters);

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id) : void
    {

        $query = $this->db->prepare('DELETE FROM expenses WHERE id = :id');
        $parameters = [
            "id" => $id
        ];
        $query->execute($parameters);
    }
}