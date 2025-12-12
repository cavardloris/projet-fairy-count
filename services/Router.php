<?php

class Router
{
    private AuthController $ac;
    private UserController $uc;
    private ExpenseController $ec;
    public function __construct()
    {
        $this->ac = new AuthController();
        $this->uc = new UserController();
        $this->ec = new ExpenseController();
    }

    public function handleRequest() : void
    {
        if(!empty($_GET['route'])) {
            if($_GET['route'] === 'login') {
                $this->ac->login();
            }
            else if($_GET['route'] === 'register') {
                $this->ac->register();
            }
            else if($_GET['route'] === 'logout') {
                $this->ac->logout();
            }
            else if($_GET['route'] === 'createExpense') {
                $this->ec->create();
            }
            else
            {
                $this->ac->notFound();
            }
        }
        else
        {
            $this->ac->home();
        }
    }
}

?>