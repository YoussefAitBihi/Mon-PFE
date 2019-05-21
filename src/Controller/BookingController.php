<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController {
   
   /**
    * Permet de faire une nouvelle réservation
    * 
    * @Route("/ads/{slug}/booking", name="book_create")
    * 
    * @IsGranted("ROLE_USER")
    *
    * @return Response
    */
   public function book(Ad $ad, ObjectManager $manager, Request $request) {
      
      $booking = new Booking();

      $form = $this->createForm(BookingType::class, $booking);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()):

         // L'utilisateur connecté
         $user = $this->getUser();

         $booking->setBooker($user)
                 ->setBookerAd($ad);

         if (!$booking->isBookableDays()):
            $this->addFlash(
               "warning",
               "Cette annonce est déja prise en compte, Veuillez choisir d'autre date"
            );
         else:

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
               "success",
               "Votre réservation est fait avec succès"
            );

            return $this->redirectToRoute('booking_show', [
               'id' => $booking->getId()
            ]);
         endif;

      endif;

      return $this->render("booking/booking.html.twig", [
         'form' => $form->createView(),
         'ad'   => $ad
      ]);

   }

   /**
    * Permet d'afficher la nouvelle réservation
    *
    * @Route("/booking/{id}", name="booking_show")
    *
    * @return Response
    */
   public function show(Booking $booking, Request $request, ObjectManager $manager) {

      $comment = new Comment();

      $form    = $this->createForm(CommentType::class, $comment);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()):

         $comment->setAuthor($this->getUser())
                 ->setAd($booking->getBookerAd());

         $manager->persist($comment);
         $manager->flush();

         $this->addFlash(
            "success",
            "Votre commentaire a bien été ajouté. Merci"
         );

         return $this->redirectToRoute("ad_show", [
            'slug' => $booking->getBookerAd()->getSlug()
         ]);

      endif;

      return $this->render("booking/show.html.twig", [
         'booking' => $booking,
         'form'    => $form->createView()
      ]);
   }

}