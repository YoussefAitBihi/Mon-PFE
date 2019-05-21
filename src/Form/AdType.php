<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title', 
                TextType::class, 
                $this->paramForm('Titre', 'Tapez un super titre pour votre annonce')
            )
            ->add(
                'slug', 
                TextType::class, 
                $this->paramForm('L\'URL de l\'annonce', 'Tapez L\'URL pour votre annonce (mais c\'est automatique', ['required' => false])
            )
            ->add(
                'price', 
                MoneyType::class, 
                $this->paramForm('Prix', 'Mais par nuit :D')
            )
            ->add(
                'introduction', 
                TextType::class, 
                $this->paramForm('L\'intoduction', 'Tapez une introduction globale de l\'annonce')
            )
            ->add(
                'content', 
                TextareaType::class, 
                $this->paramForm('Le contenu', 'Tapez une description détaillée pour votre annonce')
            )
            ->add(
                'coverImage', 
                UrlType::class, 
                $this->paramForm('Image', 'Tapez une adresse web de votre annonce')
            )
            ->add(
                'rooms', 
                IntegerType::class, 
                $this->paramForm('Le nombre de chambres', 'Tapez le nombre disponibles de chambres')
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    // Pour nous aurons le droit d'ajouter plusieurs sous-forms
                    'allow_add' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class
        ]);
    }
}