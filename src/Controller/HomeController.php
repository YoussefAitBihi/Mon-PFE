<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {
   
   /**
    * @Route("/", name="homepage")
    *
    * @return Response
    */
   public function home(AdRepository $repoAds, UserRepository $repoUsers) {
      
      return $this->render('home.html.twig', [
         'ads'    => $repoAds->getBestAds(3),
         'users'  => $repoUsers->findBestUsers()
      ]);
   }

}