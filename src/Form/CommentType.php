<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'comment',
                TextareaType::class,
                $this->paramForm(false, 'Nous serons très heureux pour votre commentaire')           
            )
            ->add(
                'rating',
                IntegerType::class,
                $this->paramForm(false, '5 étoiles sera mieux :)', [
                    'attr' => [
                        'min' => 0,
                        'max' => 5,
                        'step'=> 1
                    ],
                    'required' => false
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
