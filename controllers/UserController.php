<?php

class UserController extends AbstractController
{
    public function list() : void
    {
        if(!isset($_SESSION["id"]))
        {
            $this->redirect('index.php?route=login');
            return;
        }
        $userManager = new UserManager();
        $users = $userManager->findAll();

        $this->render('main/index.html.twig', [
            'users' => $users
        ]);
    }

}
