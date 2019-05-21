<?php

namespace App\Controller;

use App\Service\DashboardService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * Permet de retourner des informations
     * 
     * @Route("/admin", name="admin_dashboard_index")
     * 
     * @return Response 
     */
    public function index(ObjectManager $manager, DashboardService $stat) {

        // All Count Entity
        $stats      = $stat->getAllCount();
        // Best Ads
        $bestAds    = $stat->getBestAndWorstAds('DESC');
        // Worst Ads
        $worstAds   = $stat->getBestAndWorstAds('ASC');
        
        return $this->render('admin/admin_dashboard/index.html.twig', array(
            // Le nombre de ligne de chaque entity
            'stats'     => $stats,
            'bestAds'   => $bestAds,
            'worstAds'  => $worstAds
        ));
    }
}
