<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController {
    /**
     * Permet de nous retourner toutes les annonces d'utilisateur
     * 
     * @Route("/admin/ads", name="admin_ads")
     * 
     * @return Response
     */
    public function index(AdRepository $repo) {

        $ads = $repo->findAll();

        return $this->render('admin/ad/ads.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet d'afficher et modifier une annonce
     * 
     * @Route("/admin/ads/{id}-{slug}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifié"
            );

            //return $this->redirectToRoute('admin_ads');
        }

        return $this->render('admin/ad/show.html.twig', [
            'ad'    => $ad,
            'form'  => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/remove", name="admin_ads_remove")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function remove(Ad $ad, ObjectManager $manager) {

        if (count($ad->getBookings()) > 0):
            $this->addFlash(
                'warning',
                "Cette annonce <strong>{$ad->getTitle()}</strong> possède déja des réservations, et donc ne vous faut pas la supprimer"
            );
        else:
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Cette annonce <strong>{$ad->getTitle()}</strong> a été bien supprimée"
            );
        endif;

        return $this->redirectToRoute("admin_ads");

    }

}
