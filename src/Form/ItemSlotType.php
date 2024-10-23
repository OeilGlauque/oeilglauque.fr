<?php

namespace App\Form;

use App\Entity\ItemShopSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['types'] as &$value) {
            $value = $value->getType();
        }

        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de produit',
                'choices' => $options['types'],
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                }
            ])
            ->add('deliveryTime', DateTimeType::class, [
                'label' => 'Horaire de livraison',
                /*'widget' => 'single_text',
                'format' => 'd-M H:m',
                'html5' => false*/
            ])
            ->add('orderTime', DateType::class, [
                'label' => 'Horaire de fin de commande'
            ])
            ->add('preOrderTime', DateType::class, [
                'label' => 'Horaire de première commande (si la commande se fait en deux temps)'
            ])
            ->add('maxOrder', IntegerType::class, [
                'label' => 'Maximum de commande (facultatif)'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter un créneau',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemShopSlot::class,
            'types' => []
        ]);
    }
}
