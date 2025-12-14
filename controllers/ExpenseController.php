<?php

class ExpenseController extends AbstractController
{
    public function createRefund() : void
    {
        if(!isset($_SESSION["id"]))
        {
            $this->redirect('index.php?route=login');
            return;
        }

        if(!empty($_POST))
        {
            $expenseId = intval($_POST['expense_id']);
            $selectedUsers = $_POST['users_id'] ?? [];

            if($expenseId > 0 && !empty($selectedUsers))
            {
                $expenseManager = new ExpenseManager();
                $expenseShareManager = new ExpenseShareManager();
                $refundsManager = new RefundsManager();

                $expense = $expenseManager->findById($expenseId);

                if($expense)
                {
                    $creditor = $expense->getUser();

                    foreach($selectedUsers as $userId)
                    {
                        $userId = intval($userId);

                        $share = $expenseShareManager->findByExpenseAndUser($expenseId, $userId);

                        if($share)
                        {
                            $debtor = $share->getUser();
                            $amount = $share->getShareAmounts();

                            $refund = new Refund($debtor, $creditor, $amount);
                            $refundsManager->create($refund);

                            $expenseShareManager->delete($share);
                        }
                    }

                    $remainingShares = $expenseShareManager->findByExpenseId($expenseId);
                    if(empty($remainingShares))
                    {
                        $expenseManager->delete($expenseId);
                    }

                    $this->redirect('index.php?success=refunds_created');
                    return;
                }
            }
        }

        $this->redirect('index.php?error=invalid_refund_data');
    }
    public function check() : void
    {
        if(!isset($_SESSION["id"]))
        {
            $this->redirect('index.php?route=login');
            return;
        }

        if(!empty($_GET['id']))
        {
            $expenseId = intval($_GET['id']);

            if($expenseId > 0)
            {
                $expenseManager = new ExpenseManager();
                $expenseShareManager = new ExpenseShareManager();
                $refundsManager = new RefundsManager();
                $expense = $expenseManager->findById($expenseId);

                if($expense)
                {
                    $shares = $expenseShareManager->findByExpenseId($expenseId);

                    $creditor = $expense->getUser();


                    error_log("=== DEBUG CHECK EXPENSE ===");
                    error_log("Expense ID: " . $expenseId);
                    error_log("Creditor (qui a payé): " . $creditor->getName() . " (ID: " . $creditor->getId() . ")");
                    error_log("Nombre de shares: " . count($shares));


                    foreach($shares as $share)
                    {
                        $debtor = $share->getUser();
                        $amount = $share->getShareAmounts();

                        error_log("Share trouvé - Debtor: " . $debtor->getName() . " (ID: " . $debtor->getId() . "), Montant: " . $amount);

                        $refund = new Refund($debtor, $creditor, $amount);
                        $refundsManager->create($refund);
                        error_log("Remboursement créé: " . $debtor->getName() . " doit " . $amount . "€ à " . $creditor->getName());
                    }

                    error_log("=== FIN DEBUG ===");
                    $expenseShareManager->deleteByExpenseId($expenseId);
                    $expenseManager->delete($expenseId);

                    $this->redirect('index.php?success=refunds_created');
                    return;
                }
            }
        }

        $this->redirect('index.php?error=invalid_expense_id');
    }

    public function delete() : void
    {
        if(!isset($_SESSION["id"]))
        {
            $this->redirect('index.php?route=login');
            return;
        }

        if(!empty($_GET['id']))
        {
            $expenseId = intval($_GET['id']);

            if($expenseId > 0)
            {
                $expenseShareManager = new ExpenseShareManager();
                $expenseManager = new ExpenseManager();
                $expenseShareManager->deleteByExpenseId($expenseId);

                $expenseManager->delete($expenseId);

                $this->redirect('index.php?success=expense_deleted');
                return;
            }
        }

        $this->redirect('index.php?error=invalid_expense_id');
    }
    public function create() : void
    {
        if(!isset($_SESSION["id"]))
        {
            $this->redirect('index.php?route=login');
            return;
        }

        if(!empty($_POST))
        {
            $amount = floatval($_POST['amount']);
            $categoryId = intval($_POST['category_id']);
            $userId = $_SESSION['id'];
            $selectedUsers = $_POST['users_id'] ?? [];

            if($amount <= 0 || $categoryId <= 0)
            {
                $this->redirect('index.php?error=invalid_data');
                return;
            }

            $userManager = new UserManager();
            $categoryManager = new CategoriesManager();

            $user = $userManager->findById($userId);
            $category = $categoryManager->findById($categoryId);

            if($user && $category)
            {
                $expense = new Expense($user, $amount, $category);

                $expenseManager = new ExpenseManager();
                $expenseId = $expenseManager->create($expense);

                if(!empty($selectedUsers) && $expenseId)
                {
                    $expenseWithId = new Expense($user, $amount, $category, $expenseId);

                    $shareAmount = $amount / (count($selectedUsers) + 1);

                    $expenseShareManager = new ExpenseShareManager();
                    foreach($selectedUsers as $sharedUserId)
                    {
                        $sharedUserIdInt = intval($sharedUserId);
                        if($sharedUserIdInt !== $userId)
                        {
                            $sharedUser = $userManager->findById($sharedUserIdInt);
                            if($sharedUser)
                            {
                                $expenseShare = new ExpenseShare($expenseWithId, $sharedUser, $shareAmount);
                                $expenseShareManager->create($expenseShare);
                            }
                        }
                    }
                }
            }

            $this->redirect('index.php?success=expense_created');
        }
        else
        {
            $this->redirect('index.php');
        }
    }
}