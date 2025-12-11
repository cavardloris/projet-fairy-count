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
            $categories = [];

            foreach($result as $item)
            {
                $category = new Category($item["name"], $item["id"]);
                $categories[] = $category;
            }

            return $categories;
        }

        public function findById(int $id) : ? Category
        {
            $query = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
            $parameters = [
                "id" => $id
            ];
            $query->execute($parameters);
            $item = $query->fetch(PDO::FETCH_ASSOC);

            if($item)
            {
                return new Category($item["name"], $item["id"]);
            }

            return null;
        }
    }