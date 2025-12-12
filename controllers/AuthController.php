<?php

class AuthController extends AbstractController
{
    public function create() : void{
        if(!isset($_SESSION['id'])){
            $this->redirect('index.php?controller=index');
        }
    }

    public function home() : void
    {
        if(!isset($_SESSION["id"]))
        {
            $this->redirect('index.php?route=login');
            return;
        }

        $userManager = new UserManager();
        $categoryManager = new CategoriesManager();
        $expenseManager = new ExpenseManager();
        $expenseShareManager = new ExpenseShareManager();

        $refundsManager = new RefundsManager();

        $users = $userManager->findAll();
        $categories = $categoryManager->findAll();
        $expenses = $expenseManager->findAll();

        $refunds = $refundsManager->findAll();

        $expensesData = [];
        foreach($expenses as $expense) {
            $shares = $expenseShareManager->findByExpenseId($expense->getId());
            $expensesData[] = [
                'expense' => $expense,
                'shares' => $shares
            ];
        }

        $this->render('main/index.html.twig', [
            'users' => $users,
            'categories' => $categories,
            'expensesData' => $expensesData,
            'refunds' => $refunds
        ]);
    }

    public function logout() : void
    {
        session_destroy();
        $this->redirect('index.php?route=login');
    }

    public function register() : void
    {
        $error = [];

        if(!empty($_POST))
        {
            if((empty($_POST["name"])) || (empty($_POST["email"])) || (empty($_POST["password"])) || (empty($_POST["confirmPassword"])) )
            {
                $error[] = "Veuillez remplir tous les champs";
            }
            else
            {
                $ctrl = new UserManager;
                $email = $ctrl->findByEmail($_POST["email"]);
                if($email !== null)
                {
                    $error[] = "Cet utilisateur existe déjà !";
                    $this->render('auth/register.html.twig', ["errors" => $error]);
                }
                else
                {
                    if($_POST["password"] !== $_POST["confirmPassword"])
                    {
                        $error[] = "Les mots de passe ne correspondent pas !";
                        $this->render('auth/register.html.twig', ["errors" => $error]);
                    }
                    else
                    {
                        $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
                        $ctrl = new UserManager;
                        $user = new User($_POST["name"], $_POST["email"], $hashedPassword);
                        $ctrl->create($user);
                        $this->redirect("index.php?route=login");
                    }
                }
            }

        }
        else
        {
            $this->render('auth/register.html.twig', ["errors" => $error]);
        }
    }

    public function login() : void
    {
        $error = [];

        if(!empty($_POST))
        {
            if((empty($_POST["email"])) || (empty($_POST["password"])))
            {
                $error[] = "Veuillez remplir tous les champs";
            }
            else{
                $ctrl = new UserManager;
                $user = $ctrl->findByEmail($_POST["email"]);
                if($user === null)
                {
                    $error[] = "Identifiants invalides";
                    $this->render('auth/login.html.twig', ["errors" => $error]);
                }
                else
                {
                    $mdp = $user->getPassword();
                    if(password_verify($_POST["password"], $mdp))
                    {
                        $_SESSION["name"] = $user->getName();
                        $_SESSION["email"] = $user->getEmail();
                        $_SESSION["id"] = $user->getId();
                        $this->redirect('index.php');
                    }
                    else
                    {
                        $error[] = "Identifiants invalides";
                        $this->render('auth/login.html.twig', ["errors" => $error]);
                    }
                }
            }
        }
        else
        {
            $this->render('auth/login.html.twig', ["errors" => $error]);
        }

    }

    public function notFound():void
    {
        $this->render('error/notFound.html.twig', []);
    }
}