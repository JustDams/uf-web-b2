<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class ,['required' => true])
            ->add('content', TextareaType::class, ['required' => true])
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '1/5' => 1,
                    '2/5' => 2,
                    '3/5' => 3,
                    '4/5' => 4,
                    '5/5' => 5
                ], 
                'required' => true
            ])
            // ->add('idGame')
            // ->add('idUser')
            ->add('send', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
