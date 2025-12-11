<?php

    class CategoriesManager extends AbstractManager
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function findAll() : array
        {
            $query = $this->db->prepare('SELECT * FROM categories');
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
    }