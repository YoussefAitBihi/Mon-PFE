<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword', 
                PasswordType::class, 
                $this->paramForm('Mot de passe', 'Le mot de passe actuel')
            )
            ->add(
                'newPassword',
                PasswordType::class,
                $this->paramForm('Nouveau mot de passe', 'Nouveau mot de passe complexe sera mieux')
            )
            ->add(
                'passConfirm',
                PasswordType::class,
                $this->paramForm('Confirmation de mot de passe', 'Confirmer le mot de passe')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
