<?php

class ExpenseShareManager extends AbstractManager
{
    private ExpenseManager $expenseManager;
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->expenseManager = new ExpenseManager();
        $this->userManager = new UserManager();
    }

    public function findAll(): array
    {
        $query = $this->db->prepare('SELECT * FROM expenses_share');
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $shares = [];

        foreach ($results as $item) {
            $expense = $this->expenseManager->findById($item['expenses_id']);
            $user = $this->userManager->findById($item['users_id']);

            if (!$expense || !$user) {
                continue;
            }

            $shares[] = new ExpenseShare(
                $expense,
                $user,
                $item['share_amounts']
            );
        }

        return $shares;
    }

    public function findByExpenseId(int $expenseId): array
    {
        $expense = $this->expenseManager->findById($expenseId);
        if (!$expense) return [];

        $query = $this->db->prepare('SELECT * FROM expenses_share WHERE expenses_id = :eid');
        $query->execute(['eid' => $expenseId]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $shares = [];
        foreach ($results as $item) {
            $user = $this->userManager->findById($item['users_id']);
            if (!$user) continue;

            $shares[] = new ExpenseShare($expense, $user, $item['share_amounts']);
        }

        return $shares;
    }

    public function findByUserId(int $userId): array
    {
        $user = $this->userManager->findById($userId);
        if (!$user) return [];

        $query = $this->db->prepare('SELECT * FROM expenses_share WHERE users_id = :uid');
        $query->execute(['uid' => $userId]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $shares = [];
        foreach ($results as $item) {
            $expense = $this->expenseManager->findById($item['expenses_id']);
            if (!$expense) continue;

            $shares[] = new ExpenseShare($expense, $user, $item['share_amounts']);
        }

        return $shares;
    }

    public function findByExpenseAndUser(int $expenseId, int $userId): ?ExpenseShare
    {
        $query = $this->db->prepare('SELECT * FROM expenses_share WHERE expenses_id = :eid AND users_id = :uid');
        $query->execute(['eid' => $expenseId, 'uid' => $userId]);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if (!$item) return null;

        $expense = $this->expenseManager->findById($item['expenses_id']);
        $user = $this->userManager->findById($item['users_id']);

        if (!$expense || !$user) return null;

        return new ExpenseShare($expense, $user, $item['share_amounts']);
    }

    public function create(ExpenseShare $share): void
    {
        $query = $this->db->prepare('INSERT INTO expenses_share (expenses_id, users_id, share_amounts) VALUES (:eid, :uid, :amount)');
        $query->execute([
            'eid' => $share->getExpense()->getId(),
            'uid' => $share->getUser()->getId(),
            'amount' => $share->getShareAmounts()
        ]);
    }

    public function update(ExpenseShare $share): void
    {
        $query = $this->db->prepare('UPDATE expenses_share SET share_amounts = :amount WHERE expenses_id = :eid AND users_id = :uid');
        $query->execute([
            'eid' => $share->getExpense()->getId(),
            'uid' => $share->getUser()->getId(),
            'amount' => $share->getShareAmounts()
        ]);
    }

    public function delete(ExpenseShare $share): void
    {
        $query = $this->db->prepare('DELETE FROM expenses_share WHERE expenses_id = :eid AND users_id = :uid');
        $query->execute([
            'eid' => $share->getExpense()->getId(),
            'uid' => $share->getUser()->getId()
        ]);
    }

    public function deleteByExpenseId(int $expenseId) : void
    {
        $query = $this->db->prepare('DELETE FROM expenses_share WHERE expenses_id = :expense_id');
        $parameters = [
            "expense_id" => $expenseId
        ];
        $query->execute($parameters);
    }

    public function deleteByUserId(int $userId): void
    {
        $query = $this->db->prepare('DELETE FROM expenses_share WHERE users_id = :uid');
        $query->execute(['uid' => $userId]);
    }

    public function getTotalShareByExpense(int $expenseId): float
    {
        $query = $this->db->prepare('SELECT SUM(share_amounts) as total FROM expenses_share WHERE expenses_id = :eid');
        $query->execute(['eid' => $expenseId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        return (float)($result['total'] ?? 0.0);
    }

    public function userParticipatesInExpense(int $expenseId, int $userId): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) as cnt FROM expenses_share WHERE expenses_id = :eid AND users_id = :uid');
        $query->execute(['eid' => $expenseId, 'uid' => $userId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        return (int)($result['cnt'] ?? 0) > 0;
    }

    public function addMultipleParticipants(int $expenseId, array $participants): void
    {
        $expense = $this->expenseManager->findById($expenseId);
        if (!$expense) return;

        foreach ($participants as $p) {
            $user = $this->userManager->findById($p['user_id']);
            if (!$user) continue;

            $this->create(new ExpenseShare($expense, $user, $p['share']));
        }
    }

    public function splitExpenseEqually(int $expenseId, array $userIds): void
    {
        $expense = $this->expenseManager->findById($expenseId);
        if (!$expense) return;

        $total = $expense->getAmount();
        $count = count($userIds);
        if ($count === 0) return;

        $share = $total / $count;

        foreach ($userIds as $uid) {
            $user = $this->userManager->findById($uid);
            if (!$user) continue;

            if (!$this->userParticipatesInExpense($expenseId, $uid)) {
                $this->create(new ExpenseShare($expense, $user, $share));
            }
        }
    }
}
