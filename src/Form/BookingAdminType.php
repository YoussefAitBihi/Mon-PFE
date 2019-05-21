<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingAdminType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'startDate', 
                DateType::class,
                $this->paramForm(
                    'La date d\'arrivée', 
                    'La date d\'arrivée qui lui souhaite', 
                    ['widget' => 'single_text']
                )
            )
            ->add(
                'endDate', 
                DateType::class, 
                $this->paramForm(
                    'La date de fin', 
                    'La date de fin qui lui souhaite', 
                    ['widget' => 'single_text']
                )
            )
            ->add(
                'comment', 
                TextareaType::class, 
                $this->paramForm(
                    'Le commentaire',
                    'Un commentaire convenable pour cette réservation'
                )
            )
            ->add(
                'Booker', 
                EntityType::class, 
                $this->paramForm(
                    'Le voyageur',
                    false, [
                        'class'         => User::class,
                        'choice_label'  => 'lastName'
                    ]
                )
            )
            ->add(
                'BookerAd', 
                EntityType::class, 
                $this->paramForm(
                    'L\'annonce',
                    false, [
                        'class'         => Ad::class,
                        'choice_label'  => 'title'
                    ]
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups' => new GroupSequence(['Default'])
        ]);
    }
}
