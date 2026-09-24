<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('email')
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(
                        message : 'Veuillez saisir votre adresse email.',
                    ),
                    new Email(
                        message: 'Veuillez saisir une adresse email valide.',
                    ),
                ],
            ])

            ->add('plainPassword', PasswordType::class,[
                //  mapped : false parce que mon form contient plainPassword, donc un champ temporaire du formulaire.
                'mapped'=> false,
                
                'constraints'=>[
                    new NotBlank(
                        message : 'Veuillez saisir un mot de passe.'
                    ),
                    new Length(
                        min : 6,
                        max : 30,
                        minMessage: 'Le mot de passe doit contenir au moins 6 caractères.'
                        
                    )
                ],
            ])
            ->add('firstName', TextType::class,[
                'constraints'=>[
                    new NotBlank(
                        message: 'Veuillez saisir votre prénom.',
                    ),
                    new Length(
                        min : 4,
                        max : 30,
                        minMessage: 'Le prénom doit contenir 4 caractères minimum.'
                    )
                ],
            ])
            ->add('lastName', TextType::class,[
                'constraints'=>[
                    new NotBlank(
                        message: 'Veuillez saisir votre nom.',
                    ),
                    new Length(
                        min : 4,
                        max : 30,
                        minMessage: 'Le nom doit contenir 4 caractères minimum.'
                    )
                ],
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
