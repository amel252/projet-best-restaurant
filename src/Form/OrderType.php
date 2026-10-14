<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addresses', EntityType::class, [
                'label'=> "Choisissez votre adresse de livraison",
                'required'=> true,
                'class'=> Address::class,
                //  pour le radios "expanded"
                'expanded'=> true,
                'choices' => $options['addresses'],
                'label_html'=> true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez choisir une adresse de livraison.'),
                ],
            ])
            ->add('carriers', EntityType::class, [
                'label'=> "Choisissez votre transporteur",
                'required'=> true,
                'class'=> Carrier::class,
                'expanded'=> true,
                'choices' => $options['carriers'],
                
                'choice_label' => function (Carrier $carrier): string { 
                    return sprintf( 
                        '%s — %s €', 
                        $carrier->getName(), 
                        number_format($carrier->getPrice() ?? 0, 2, ',', ' ') 
                    );
                },
                'label_html'=> false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez choisir un transporteur.'),
                ],
            ])
             // j'ai ajouté le btn submit avec add puis j'ai importé en haut sur la doc 
            ->add('Submit', SubmitType::class,[
                'label'=> "Valider",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'addresses' =>[],
            'carriers'=>[],
        ]);
    }
}
