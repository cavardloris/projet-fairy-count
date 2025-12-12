<?php

class RefundsManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();        
    }

    public function findAll() : array
    {
        $query = $this->db->prepare('SELECT refunds.*, d.id as debtor_id,
                    d.name as debtor_name,
                    d.email as debtor_email,
                    d.password as debtor_password,
                    c.id as creditor_id,
                    c.name as creditor_name,
                    c.email as creditor_email, 
                    c.password as creditor_password FROM refunds INNER JOIN users d ON refunds.debtor_id = d.id INNER JOIN users c ON refunds.creditor_id = c.id ');
        $parameters = [

        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $refunds = [];

        foreach($result as $item)
        {
            $debtor = new User($item["debtor_name"], $item["debtor_email"], $item['debtor_password'], $item['debtor_id']);
            $creditor = new User($item["creditor_name"], $item["creditor_email"], $item['creditor_password'], $item['creditor_id']);
            $refund = new Refund($debtor, $creditor, $item['amount'], $item['id']);
            $refunds[] = $refund;
        }

        return $refunds;
    }

    public function findById(int $id) : ? Refund
    {
        $query = $this->db->prepare('SELECT refunds.*, d.id as debtor_id,
                    d.name as debtor_name,
                    d.email as debtor_email,
                    d.password as debtor_password,
                    c.id as creditor_id,
                    c.name as creditor_name,
                    c.email as creditor_email, 
                    c.password as creditor_password FROM refunds INNER JOIN users d ON refunds.debtor_id = d.id INNER JOIN users c ON refunds.creditor_id = c.id  WHERE refunds.id = :id');
        $parameters = [
            "id" => $id
        ];
        $query->execute($parameters);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if($item)
        {
            $debtor = new User($item["debtor_name"], $item["debtor_email"], $item['debtor_password'], $item['debtor_id']);
            $creditor = new User($item["creditor_name"], $item["creditor_email"], $item['creditor_password'], $item['creditor_id']);
            return new Refund($debtor, $creditor, $item['amount'], $item['id']);
        }

        return null;
    }

}