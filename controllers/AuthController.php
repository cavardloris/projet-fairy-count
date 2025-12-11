<?php

    class AuthController extends AbstractController
    {
        public function home() : void
        {
            $this->render('home/home.html.twig', []);
        }
    }