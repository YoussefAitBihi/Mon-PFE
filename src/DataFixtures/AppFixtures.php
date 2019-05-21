<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{  
   
   private $encoder;

   public function __construct(UserPasswordEncoderInterface $encoder) {
      $this->encoder = $encoder;
   }

   public function load(ObjectManager $manager)
   {
      // Pour créer des données au hasard
      $faker = Factory::create('fr-FR');

      // Création du rôle
      $adminRole = new Role();
      $adminRole->setTitle('ROLE_ADMIN');
      $manager->persist($adminRole);

      $userAdmin = new User();

      // Administrateur
      $userAdmin->setFirstName('Youssef')
                ->setLastName('Ait Bihi')
                ->setEmail('youssefaitbihi@gmail.com')
                ->setPicture('http://placehold.it/100x100')
                ->setHash($this->encoder->encodePassword($userAdmin, 'password'))
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->addUserRole($adminRole);

      $manager->persist($userAdmin);
      
      // Création des utilisateurs (des gérants)
      $users = [];
      $genre = ["men", "women"];

      for ( $i = 0; $i <= 10; $i++ ):

         $user = new User();

         $numberAl = mt_rand(1, 99);
         $pic = "https://randomuser.me/api/portraits/".$genre[mt_rand(0, 1)]."/" . $numberAl .".jpg";

         // Hashage du mot de passe
         $hash = $this->encoder->encodePassword($user, 'password');

         $user->setFirstName($faker->firstname)
              ->setLastName($faker->lastname)
              ->setEmail($faker->email)
              ->setPicture($pic)
              ->setHash($hash)
              ->setIntroduction($faker->sentence())
              ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>');

         // Sauvegarder les données de l'utilisateur 
         $manager->persist($user);

         // Sauvegarder les données
         $users[] = $user;   
      endfor;

      // Création des annonces
      for ( $i = 1; $i <= 30; $i++ ) {

         // Création d'objet d'Ad
         $ad = new Ad();

         $title = $faker->sentence();
         $introduction = $faker->paragraph(2);
         $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
         $coverImage = $faker->imageUrl(1000, 300);

         // User Aléatoire
         $user = $users[mt_rand(0, count($users) - 1)];

         // Sert à remplir les propiétés de la classe Ad
         $ad->setTitle($title)
            ->setPrice(mt_rand(100, 500))
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setCoverImage($coverImage)
            ->setRooms(mt_rand(3, 5))
            ->setAuthor($user);

         for ( $j = 1; $j <= mt_rand(3, 5); $j++ ) {
            
            $image = new Image();

            $image->setUrl($faker->imageUrl(1000, 300))
                  ->setCaption($faker->sentence())
                  ->setAd($ad);

            $manager->persist($image);
         }

         // Sert à persister
         $manager->persist($ad);

         for ($k = 0; $k <= 10; $k++){
            // New Booker
            $booking = new Booking();
            
            // User Aléatoire
            $userBooker = $users[mt_rand(0, count($users) - 1)];
            // Le départ de reservation
            $startDate  = $faker->dateTimeBetween('-3 months');
            // La durée de réservation
            $duration   = mt_rand(3, 20);
            // La fin de réservation
            $endDate    = (clone $startDate)->modify("+$duration");
            // La date de la réservation
            $createdAt  = $faker->dateTimeBetween('-4 months');
            // Le count de la réservation
            $amount     = $ad->getPrice() * $duration;
            // Le commentaire
            $comment    = $faker->paragraph(mt_rand(1, 3));

            $booking->setBooker($userBooker)
                   ->setBookerAd($ad)
                   ->setStartDate($startDate)
                   ->setEndDate($endDate)
                   ->setCreatedAt($createdAt)
                   ->setAmount($amount)
                   ->setComment($comment);

            $manager->persist($booking);

            for ($c = 0; $c <= mt_rand(1, 2); $c++) {

               $comment = new Comment();

               $comment->setComment($faker->paragraph())
                       ->setRating(mt_rand(3, 5))
                       ->setAuthor($booking->getBooker())
                       ->setAd($ad);

               // Sauvegarder les données
               $manager->persist($comment);
            }
            
         }

      }
      
      // Sert à ajouter le contenu dans la DB
      $manager->flush();
   }
}
