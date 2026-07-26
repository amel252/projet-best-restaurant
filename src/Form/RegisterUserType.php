<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class,[
                //  mapped : false parce que mon form contient plainPassword, donc un champ temporaire du formulaire.
                'mapped'=> false,
                
                'constraints'=>[
                    new NotBlank(),
                    new Length(
                        min : 4,
                        max : 30
                    )
                ],
            ])
            ->add('firstName', TextType::class,[
                'constraints'=>[
                    new Length(
                        min : 4,
                        max : 30
                    )
                ],
            ])
            ->add('lastName', TextType::class,[
                'constraints'=>[
                    new Length(
                        min : 4,
                        max : 30
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //  on précise qu'on veux un email unique
            // 'constraints'=>[
            //         new UniqueEntity([
            //             'entityClass'=>User::class,
            //             'fields'=> 'email'
            //         ])
            //     ],
            'data_class' => User::class,
        ]);
    }
}
