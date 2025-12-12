<?php

class RefundsManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();        
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT refunds.*,  FROM refunds');
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

}