<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class HomeController extends AbstractController
    {
        /**
         * @Route("/", name="homepage")
         */
        public function home()
        {
            // la route "/" renvoie la page "home.html.twig" qui est la page d'accueil
            Return $this->render('home.html.twig');
        }
    }
