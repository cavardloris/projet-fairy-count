<?php

class ExpenseShareManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll(): array
    {
        $query = $this->db->prepare('SELECT * FROM expenses_share');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenseShares = [];

        foreach($result as $item)
        {
            $expenseManager = new ExpenseManager();
            $userManager = new UserManager();
            
            $expense = $expenseManager->findById($item["expenses_id"]);
            $user = $userManager->findById($item["users_id"]);
            
            $expenseShare = new ExpenseShare(
                $expense,
                $user,
                $item["share_amounts"]
            );
            $expenseShares[] = $expenseShare;
        }

        return $expenseShares;
    }

    public function findByExpenseId(int $expenseId): array
    {
        $query = $this->db->prepare('SELECT * FROM expenses_share WHERE expenses_id = :expenses_id');
        $parameters = [
            "expenses_id" => $expenseId
        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenseShares = [];

        $expenseManager = new ExpenseManager();
        $expense = $expenseManager->findById($expenseId);
        
        if (!$expense) {
            return [];
        }

        $userManager = new UserManager();

        foreach($result as $item)
        {
            $user = $userManager->findById($item["users_id"]);
            
            $expenseShare = new ExpenseShare(
                $expense,
                $user,
                $item["share_amounts"]
            );
            $expenseShares[] = $expenseShare;
        }

        return $expenseShares;
    }

    public function findByUserId(int $userId): array
    {
        $query = $this->db->prepare('SELECT * FROM expenses_share WHERE users_id = :users_id');
        $parameters = [
            "users_id" => $userId
        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $expenseShares = [];

        $userManager = new UserManager();
        $user = $userManager->findById($userId);
        
        if (!$user) {
            return [];
        }

        $expenseManager = new ExpenseManager();

        foreach($result as $item)
        {
            $expense = $expenseManager->findById($item["expenses_id"]);
            
            $expenseShare = new ExpenseShare(
                $expense,
                $user,
                $item["share_amounts"]
            );
            $expenseShares[] = $expenseShare;
        }

        return $expenseShares;
    }

    public function findByExpenseAndUser(int $expenseId, int $userId): ?ExpenseShare
    {
        $query = $this->db->prepare('SELECT * FROM expenses_share WHERE expenses_id = :expenses_id AND users_id = :users_id');
        $parameters = [
            "expenses_id" => $expenseId,
            "users_id" => $userId
        ];
        $query->execute($parameters);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if($item)
        {
            $expenseManager = new ExpenseManager();
            $userManager = new UserManager();
            
            $expense = $expenseManager->findById($item["expenses_id"]);
            $user = $userManager->findById($item["users_id"]);
            
            return new ExpenseShare(
                $expense,
                $user,
                $item["share_amounts"]
            );
        }

        return null;
    }

    public function create(ExpenseShare $expenseShare): void
    {
        $query = $this->db->prepare('INSERT INTO expenses_share (expenses_id, users_id, share_amounts) VALUES (:expenses_id, :users_id, :share_amounts)');
        $parameters = [
            "expenses_id" => $expenseShare->getExpense()->getId(),
            "users_id" => $expenseShare->getUser()->getId(),
            "share_amounts" => $expenseShare->getShareAmounts()
        ];
        $query->execute($parameters);
    }

    public function update(ExpenseShare $expenseShare): void
    {
        $query = $this->db->prepare('UPDATE expenses_share SET share_amounts = :share_amounts WHERE expenses_id = :expenses_id AND users_id = :users_id');
        $parameters = [
            "expenses_id" => $expenseShare->getExpense()->getId(),
            "users_id" => $expenseShare->getUser()->getId(),
            "share_amounts" => $expenseShare->getShareAmounts()
        ];
        $query->execute($parameters);
    }

    public function delete(ExpenseShare $expenseShare): void
    {
        $query = $this->db->prepare('DELETE FROM expenses_share WHERE expenses_id = :expenses_id AND users_id = :users_id');
        $parameters = [
            "expenses_id" => $expenseShare->getExpense()->getId(),
            "users_id" => $expenseShare->getUser()->getId()
        ];
        $query->execute($parameters);
    }

    public function deleteByExpenseId(int $expenseId): void
    {
        $query = $this->db->prepare('DELETE FROM expenses_share WHERE expenses_id = :expenses_id');
        $parameters = [
            "expenses_id" => $expenseId
        ];
        $query->execute($parameters);
    }

    public function deleteByUserId(int $userId): void
    {
        $query = $this->db->prepare('DELETE FROM expenses_share WHERE users_id = :users_id');
        $parameters = [
            "users_id" => $userId
        ];
        $query->execute($parameters);
    }

    public function getTotalShareByExpense(int $expenseId): float
    {
        $query = $this->db->prepare('SELECT SUM(share_amounts) as total FROM expenses_share WHERE expenses_id = :expenses_id');
        $parameters = [
            "expenses_id" => $expenseId
        ];
        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return (float) ($result['total'] ?? 0.0);
    }

    public function userParticipatesInExpense(int $expenseId, int $userId): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) as count FROM expenses_share WHERE expenses_id = :expenses_id AND users_id = :users_id');
        $parameters = [
            "expenses_id" => $expenseId,
            "users_id" => $userId
        ];
        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return (int) ($result['count'] ?? 0) > 0;
    }

    public function addMultipleParticipants(int $expenseId, array $participants): void
    {
        foreach ($participants as $participant) {
            $expenseManager = new ExpenseManager();
            $userManager = new UserManager();
            
            $expense = $expenseManager->findById($expenseId);
            $user = $userManager->findById($participant['user_id']);
            
            $expenseShare = new ExpenseShare(
                $expense,
                $user,
                $participant['share']
            );
            
            $this->create($expenseShare);
        }
    }

    public function splitExpenseEqually(int $expenseId, array $userIds): void
    {
        $expenseManager = new ExpenseManager();
        $expense = $expenseManager->findById($expenseId);
        
        if (!$expense) {
            throw new Exception("Dépense non trouvée");
        }
        
        $totalAmount = $expense->getAmount();
        $numberOfParticipants = count($userIds);
        $sharePerPerson = $totalAmount / $numberOfParticipants;
        
        foreach ($userIds as $userId) {
            $userManager = new UserManager();
            $user = $userManager->findById($userId);
            
            if ($user) {
                $expenseShare = new ExpenseShare($expense, $user, $sharePerPerson);
                $this->create($expenseShare);
            }
        }
    }
}