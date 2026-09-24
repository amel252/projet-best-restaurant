<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                'expanded'=> true,
                'choices' => $options['addresses'],
                'label_html'=> true 
            ])
            ->add('carriers', EntityType::class, [
                'label'=> "Choisissez votre transporteur",
                'required'=> true,
                'class'=> Carrier::class,
                'expanded'=> true,
                'choices' => $options['carriers'],
                'label_html'=> true 
            ])
             // j'ai ajouté le btn submit avec add puis j'ai pris le lien en haut sur la doc 
            ->add('Submit', SubmitType::class,[
                'label'=> "Valider",
                'attr'=>[
                    'class'=> "btn btn-success"
                ]
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
