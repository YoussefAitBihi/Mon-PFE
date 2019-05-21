<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountEditType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName', 
                TextType::class, 
                $this->paramForm("Le premier nom", "C'est le premier nom")
            )
            ->add(
                'lastName',
                TextType::class,
                $this->paramForm("Le deuxième nom", "C'est le deuxième nom")
            )
            ->add(
                'email',
                EmailType::class,
                $this->paramForm("L'email", "C'est l'email")    
            )
            ->add('picture')
            ->add(
                'description',
                TextareaType::class,
                $this->paramForm("La description", "C'est la description")    
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
