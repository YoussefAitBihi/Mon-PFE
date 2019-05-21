<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountEditType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Permet de nous retourner tous les utilisateurs
     * 
     * @Route("/admin/users/{page}", name="admin_users_index", requirements={"page": "\d+"})
     * 
     * @return Response
     */
    public function index(PaginationService $pagination, $page = 1) {

        $pagination->setEntityClassName(User::class)
                   ->setCurrentPage($page);

        return $this->render('admin/admin_user/index.html.twig', [
            'users' => $pagination->getData(),
            'pages' => $pagination->allPages(),
            'currentPage' => $page
        ]);
    }

    /**
     * Permet d'afficher et d'éditer un utilisteur
     *
     * @Route("/admin/users/{id}/edit", name="admin_user_edit")
     * 
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * 
     * @return Response
     */
    public function edit(User $user, Request $request, ObjectManager $manager) {

        $form = $this->createForm(AccountEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                "success",
                "Cet utilisateur {$user->getFullName()} a été bien modifié"
            );

            return $this->redirectToRoute("admin_users_index");

        endif;

        return $this->render('admin/admin_user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);

    }

    /**
     * Permet de supprimer un utilisateur
     * 
     * @Route("/admin/users/{id}/remove", name="admin_user_remove")
     *
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function remove(User $user, ObjectManager $manager) {

        // if (count($user->getAds()) > 0) {
        //     $manager->remove($user->getAds());
        // }

        // if (count($user->getComments()) > 0) {
        //     $manager->remove($user->getComments());
        // }

        // if (count($user->getBookings()) > 0) {
        //     $manager->remove($user->getBookings());
        // }

        if (count($user->getAds()) > 0) {

            $this->addFlash(
                "warning",
                "Vous ne pouvez pas supprimer cet utilisateur, car il possède des commentaires, ou des annonces, ou des réservations"
            );

        } else {
            $manager->remove($user);
            $manager->flush();
    
            $this->addFlash(
                "success",
                "Cet utilisateur : <strong>{$user->getFullName()}</strong> a été bien supprimé"
            );
    
            return $this->redirectToRoute("homepage");
        }

    }

}
