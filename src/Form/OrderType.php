<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('lastName', null, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('email', null, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('phone', null, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('adress', null, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            // ->add('createdAt', null, [
            //    'widget' => 'single_text',
            //])
            ->add('city', EntityType::class, [
                'label' => 'Ville',
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => [
                        'class' => 'form form-control'
                    ]
                
            ])
            ->add('payOnDelivery', null, [
                'label' => 'Je desire payer à la livraison '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
