<?php

    class AuthController extends AbstractController
    {
        public function home() : void
        {
            $this->render('home/home.html.twig', []);
        }

        public function logout() : void
        {
            session_destroy();
            $this->redirect('index.php');
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
                                $this->redirect("index.php");
                        }
                    }
                }

            }
            else
            {
                $this->render('auth/register.html.twig', ["errors" => $error]);
            }
        }
    }