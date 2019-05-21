<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                $this->paramForm('Prénom', 'Votre prénom ...!')
            )
            ->add(
                'lastName',
                TextType::class,
                $this->paramForm('Nom', 'Votre nom ...!')
            )
            ->add(
                'email',
                EmailType::class,
                $this->paramForm('Email', 'Votre email ( sera donc votre nom d\'utilisateur )')
            )
            ->add(
                'picture',
                UrlType::class,
                $this->paramForm('Photo', 'Votre photo personnel ( mais c\'est optionnel )', false)
            )
            ->add(
                'hash',
                PasswordType::class,
                $this->paramForm('Le mot de passe', 'Un mot de passe compliqué sera mieux')
            )
            ->add(
                'passConfirm',
                PasswordType::class,
                $this->paramForm('Confirmation du mot de passe', 'Veuillez confirmer le mot de passe')
            )
            ->add(
                'introduction',
                TextType::class,
                $this->paramForm('L\'introduction', 'Une introduction dans quelques mots')
            )
            ->add(
                'description',
                TextareaType::class,
                $this->paramForm('La description', 'Présentez-vous plus précisement')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
