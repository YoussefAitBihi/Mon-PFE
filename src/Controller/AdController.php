<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Permet de nous retourner toutes les annonces
     * 
     * @Route("/ads", name="ad_index")
     * 
     * @return Response
     */
    public function index(AdRepository $repo) {
        
        // $repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();
        
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
    /**
     * Cette fonction nous permet de créer une nouvelle annonce
     * 
     * @Route("/ads/new", name="ad_new")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function newAd(Request $request, ObjectManager $manager) {

        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
        
        // Manipuler la requête ( il va relier la requête avec l'entité )
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ):

            foreach ( $ad->getImages() as $image ):
                $image->setAd($ad);
                $manager->persist($image);
            endforeach;

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                "success",
                "Votre annonce " . $ad->getTitle() . " a été bien enregistrée"
            );

            return $this->redirectToRoute('ad_show', [
                'slug' => $ad->getSlug()
            ]);

        endif;

        return $this->render('ad/newAd.html.twig',[

            'form' => $form->createView()

        ]);
    }

    /**
     * Permet de modifier l'annonce via son slug !
     * 
     * @Route("/ads/{slug}/edit", name="ad_edit")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        // Vérification du form
        if ( $form->isSubmitted() && $form->isValid() ):

            // AJouter les imgaes qui viennent de la requête
            foreach ( $ad->getImages() as $image ):
                $image->setAd($ad);
                $manager->persist($image);
            endforeach;

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                "success",
                "Votre annonce " . $ad->getTitle() . " a bien été modifiée !"
            );

            return $this->redirectToRoute(
                'ad_show',
                [
                    'slug' => $ad->getSlug()
                ]
            );

        endif;

        return $this->render(
            'ad/editAd.html.twig', [
                'form' => $form->createView(),
                'ad'   => $ad
        ]);
    }

    /**
     * Permet de supprimer une annonce via son slug
     * 
     * @Route("/ads/{slug}/remove", name="ad_remove")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()")
     *
     * @param ObjectManager $manager
     * @param Ad $ad
     * @return Response
     */
    public function removeAd(ObjectManager $manager, Ad $ad) {

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            "success",
            "Cette annonce <strong>{ad.getTitle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("ad_index");

    }

    /**
     * Permet de nous retourner une annonce via son slug
     * 
     * @Route("/ads/{slug}", name="ad_show")
     * 
     * @return Response
     */
    public function showAd(Ad $ad) {
 
        return $this->render('ad/show.html.twig', [
            'ad'    => $ad
        ]);

    }

}