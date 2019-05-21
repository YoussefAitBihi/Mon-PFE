<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingAdminType;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Permet de représenter toutes les réservations
     * 
     * @Route("/admin/bookings/{page}", name="admin_bookings_index", requirements={"page": "\d+"})
     * 
     * @return Response
     */
    public function index(PaginationService $pagination, $page = 1) {

        $pagination->setEntityClassName(Booking::class)
                   ->setLimit(8)
                   ->setCurrentPage($page);

        return $this->render('admin/admin_booking/index.html.twig', [
            'bookings'      => $pagination->getData(),
            'pages'         => $pagination->allPages(),
            'currentPage'   => $page
        ]);
    }

    /**
     * Permet d'afficher et modifier une réservation
     * 
     * @Route("/admin/bookings/{id}/edit",name="admin_booking_edit")
     *
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return void
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager) {

        $form = $this->createForm(BookingAdminType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "Cette réservation <em>{$booking->getBookerAd()->getTitle()}</em> a été bien modifiée"
            );

            return $this->redirectToRoute("admin_bookings_index");

        endif;

        return $this->render('admin/admin_booking/edit.html.twig', [
            'form'      => $form->createView(),
            'booking'   => $booking
        ]);
    }
}
