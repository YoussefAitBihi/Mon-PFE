<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController {

    /**
     * Permet de nous retourner l'utilisateur par son slug
     * 
     * @Route("/user/{slug}", name="user")
     * 
     * @return Response
     */
    public function index(User $user) {
        return $this->render('user/index.html.twig', [
            'user' => $user   
        ]);
    }

    /**
     * Permet d'afficher seulement les annonces
     * 
     * @Route("/user/{slug}/show", name="user_show_ads")
     *
     * @return Response
     */
    public function show(User $user) {
        return $this->render("user/show.html.twig", [
            'user' => $user
        ]);
    }

}
