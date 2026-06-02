<?php

namespace App\Form\Type;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Opcja odpowiedzi', 'class' => 'form-control']
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => 'Poprawna',
                'required' => false,
                'attr' => ['class' => 'form-check-input ms-2', 'title' => 'Zaznacz jeśli ta odpowiedź jest prawidłowa']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
