<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountEditType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
   
   /**
    * Permet d'afficher le formulaire RegistrationType et créer un utilisateur
    * 
    * @Route("/register", name="account_register")
    *
    * @return Response
    */
   public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {

      $user = new User();

      // Création du formulaire
      $form = $this->createForm(RegistrationType::class, $user);

      // Récupération de données de l'utilisateur via REQUEST
      $form->handleRequest($request);

      // Vérification du formulaire
      if ($form->isSubmitted() && $form->isValid()):

         // Encoder le mot de passe
         $hash = $encoder->encodePassword($user, $user->getHash());
         $user->setHash($hash);

         $manager->persist($user);
         $manager->flush();

         // Le flush de succès
         $this->addFlash(
               "success",
               "Vous vous êtes inscrit avec succès, vous pouvez donc vous connecter !"
         );

         // Redirection vers la page de login
         return $this->redirectToRoute('account_login');

      endif;

      return $this->render('account/registration.html.twig', [
         'form' => $form->createView()
      ]);
   }

   /**
    * Permet de nous connecter
    * 
    * @Route("/login", name="account_login")
    * 
    * @return Response
    */
   public function login(AuthenticationUtils $utils) {

      $error          = $utils->getLastAuthenticationError();
      $getLastEmail   = $utils->getLastUsername();

      return $this->render('account/login.html.twig', [
         'erreur'          => $error !== null,
         'lastUserName'    => $getLastEmail
      ]);
   }

   /**
    * Nous permettre de nous déconnecter
    *
    * @Route("/logout", name="account_logout")
    * 
    * @return void
    */
   public function logout() {
      // Rien :D
   }

   /**
    * Permet d'afficher le profil de l'utilisateur et les modifier
    * 
    * @Route("/account/profile", name="account_profile")
    * @IsGranted("ROLE_USER")
    *
    * @return Response
    */
   public function profile(Request $request, ObjectManager $manager) {

      // L'utilisateur connecté
      $user = $this->getUser();

      // Création du formulaire
      $form = $this->createForm(AccountEditType::class, $user);

      // Gérer la requête ( Récupération de données )
      $form->handleRequest($request);
      
      if ( $form->isSubmitted() && $form->isValid() ):

         $manager->persist($user);
         $manager->flush();

         $this->addFlash(
            "success",
            "Vos modifications a bien passé correctement"
         );

         return $this->redirectToRoute("homepage");

      endif;

      return $this->render('account/profile.html.twig', [
         'form' => $form->createView()
      ]);

   }

   /**
    * Permet de modifier le mot de passe
    * 
    * @Route("/account/password-update", name="update-password")
    * @Security("is_granted('ROLE_USER') and user === '{$this->getUser()}'", message="Il vous faut vous connecter !")
    *
    * @return Response
    */
   public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager) {

      $password = new PasswordUpdate();

      $user = $this->getUser();

      $form = $this->createForm(PasswordUpdateType::class, $password);

      $form->handleRequest($request);

      if ( $form->isSubmitted() && $form->isValid() ):
         // Le mot de passe actuel
         $pass = $password->getOldPassword();

         // Permet de comparer entre une chaine et un hash
         if (password_verify($pass, $user->getHash())):

            // Nouveau mot de passe
            $newPass = $password->getNewPassword();
            // Mot de passe hashé
            $newPassHashed = $encoder->encodePassword($user, $newPass);

            $user->setHash($newPassHashed);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
               "success",
               "Votre <strong>mot de passe</strong> a été bien <strong>modifié</strong>"
            );

            return $this->redirectToRoute("account_logout");
         
         else:
            $form->get("oldPassword")
                 ->addError(new FormError("Le mot de passe est incorrect ! réessayer"));
         endif;

      endif;

      return $this->render(
         'account/password.html.twig', [
            'form' => $form->createView()
      ]);
   }

   /**
    * Permet d'afficher la page d'utilisateur connecté
    *
    * @Route("/account", name="account_index")
    *  
    * @IsGranted("ROLE_USER")
    * 
    * @return Response
    */
   public function myAccount() {
      
      return $this->render('user/index.html.twig', [
         'user' => $this->getUser()
      ]);

   }

   /**
    * Permet de retourner les réservations d'utilisateur
    * 
    * @IsGranted("ROLE_USER")
    * 
    * @Route("/account/bookings", name="account_bookings")
    * 
    * @return Response
    */
   public function myBookings() {
      return $this->render('account/bookings.html.twig');
   }
}