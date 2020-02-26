<?php

    namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class HomeController extends AbstractController
    {
        /**
         * @Route("/", name="homepage")
         */
        public function home(AdRepository $adRepo, UserRepository $userRepo)
        {
            // la route "/" renvoie la page "home.html.twig" qui est la page d'accueil
            Return $this->render('home.html.twig', [
                'ads' => $adRepo->findBestAds(3),
                'users' => $userRepo->findBestUsers(2)
            ]);
        }
    }
