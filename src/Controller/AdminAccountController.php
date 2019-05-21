<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController {
    /**
     * Permet de nous connecter
     * 
     * @Route("/admin/login", name="admin_account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils) {

        $error      = $utils->getLastAuthenticationError();
        $lastEmail  = $utils->getLastUsername();

        return $this->render('admin/admin_account/login.html.twig', [
            'error'     => $error,
            'lastEmail' => $lastEmail
        ]);
    }

    /**
     * Permet de nous déconnecter
     * 
     * @Route("/admin/logout", name="admin_account_logout")
     *
     * @return void
     */
    public function logout() {

    }

    
}
