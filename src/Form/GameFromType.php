<?php

namespace App\Form;

use App\Entity\Games;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['required' => true])
            ->add('genres', TextType::class, ['required' => true])
            ->add('publishers', TextType::class, ['required' => true])
            ->add('review_score', NumberType::class, ['required' => true])
            ->add('price', MoneyType::class, ['required' => true])
            ->add('console', TextType::class, ['required' => true])
            ->add('releaseYear', NumberType::class, ['required' => true])
            ->add('description', TextareaType::class, ['required' => true])
            ->add('stock', NumberType::class, ['required' => true])
            ->add('create', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
