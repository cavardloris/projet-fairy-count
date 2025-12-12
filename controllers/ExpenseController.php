<?php

class ExpenseController extends AbstractController
{
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
                        $sharedUser = $userManager->findById(intval($sharedUserId));
                        if($sharedUser)
                        {
                            $expenseShare = new ExpenseShare($expenseWithId, $sharedUser, $shareAmount);
                            $expenseShareManager->create($expenseShare);
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