<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class DashboardService {
   
   private $manager;

   public function __construct(ObjectManager $manager) {
      $this->manager = $manager;
   }

   public function getAllCount() {

      $users    = $this->getCountUsers();
      $ads      = $this->getCountAds();
      $bookings = $this->getCountBookings();
      $comments = $this->getCountComments();

      return compact('users', 'ads', 'bookings', 'comments');
   }

   public function getCountUsers() {
      return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
   }

   public function getCountAds() {
      return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
   }

   public function getCountBookings() {
      return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
   }

   public function getCountComments() {
      return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
   }

   public function getBestAndWorstAds($order) {
      return $this->manager->createQuery(
         "SELECT AVG(c.rating) AS note, a.title, u.lastName, u.picture
         FROM App\Entity\Comment c
         JOIN c.ad a
         JOIN a.author u
         GROUP BY a
         ORDER BY note $order"
      )->setMaxResults(5)
       ->getResult();
   }

}