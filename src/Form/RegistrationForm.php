<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => ['placeholder' => 'example@example.com', 'autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank(message: 'Enter a valid email address'),
                    new Email(message: 'Email should be format: example@example.com'),
                    new Length(
                        min: 6,
                        max: 100,
                        minMessage: 'Email should be at least 8 characters long',
                        maxMessage: 'Length cannot be longer than 100 characters'),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'attr' => ['placeholder' => 'Enter your username', 'autocomplete' => 'username'],
                'constraints' => [
                    new NotBlank(message: 'Username cannot be empty'),
                    new Length(
                        min: 3,
                        max: 50,
                        minMessage: 'Username must be at least 3 characters',
                        maxMessage: 'Username cannot be longer than 50 characters',
                    ),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => 'Username can contain only letters, numbers, and underscores',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password', 'attr' => ['placeholder' => 'Password']],
                'second_options' => ['label' => 'Repeat Password'],
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'This field is required.']),
                    new Length(['min' => 6, 'max' => 40, 'minMessage'=> 'Min length {{ limit }} characters.' ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                        'message' => 'The password must contain at least one uppercase letter, one lowercase letter, and one digit.',
                    ]),

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
