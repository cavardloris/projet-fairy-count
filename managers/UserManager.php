<?php

class UserManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT * FROM users');
        $parameters = [

        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach($result as $item)
        {
            $user = new User($item["name"], $item["email"], $item["password"], $item["id"]);
            $users[] = $user;
        }

        return $users;
    }

    public function findById(int $id) : ? User
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $parameters = [
            "id" => $id
        ];
        $query->execute($parameters);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if($item)
        {
            return new User($item["name"], $item["email"], $item["password"], $item["id"]);
        }

        return null;
    }

    public function findByEmail(string $email) : ? User
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $parameters = [
            "email" => $email
        ];
        $query->execute($parameters);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if($item)
        {
            return new User($item["name"], $item["email"], $item["password"], $item["id"]);
        }

        return null;
    }

    public function create(User $user) : void
    {
        $query = $this->db->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');;
        $parameters = [
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "password" => $user->getPassword()
        ];
        $query->execute($parameters);
    }

    public function update(User $user) : void
    {
        $query = $this->db->prepare('UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id');;
        $parameters = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "password" => $user->getPassword()
        ];
        $query->execute($parameters);
    }

    public function delete(User $user) : void
    {
        $query = $this->db->prepare('DELETE FROM users WHERE id = :id');;
        $parameters = [
            "id" => $user->getId()
        ];
        $query->execute($parameters);
    }

    public function countAll() : int
    {
        $query = $this->db->prepare('SELECT COUNT(*) AS count FROM users');
        
        $query->execute();
        
        $result = $query->fetch(PDO::FETCH_ASSOC);

        return (int) $result['count'];
    }
}
