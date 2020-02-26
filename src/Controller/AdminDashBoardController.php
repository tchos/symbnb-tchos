<?php

namespace App\Controller;

use App\Service\Statistiques;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashBoardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, Statistiques $statistiques)
    {
        //$users = $statistiques->getUsersCount();
        //$ads = $statistiques->getAdsCount();
        //$bookings = $statistiques->getBookingsCount();
        //$comments = $statistiques->getCommentsCount();

        $stats = $statistiques->getStats();

        $bestAds = $statistiques->getAdsStats('DESC');
        $worstAds = $statistiques->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
