<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                    'autofocus' => true,
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Hasło',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // Disable validation groups for login, let Symfony Security handle errors
            'validation_groups' => false,
            'csrf_token_id' => 'authenticate',
        ]);
    }

    public function getBlockPrefix(): string
    {
        // By returning an empty string, the form inputs will be named "email" and "password" instead of "login_form[email]".
        return '';
    }
}
