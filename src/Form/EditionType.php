<?php

namespace App\Form;

use App\Entity\Edition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('annee', ChoiceType::class, [
                'label' => 'Année :',
                'choices' => range((int)date("Y"),(int)date("Y")+5),
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                }/*
                'label_attr' => [
                    'class' => 'col-md-4'
                ],
                'row_attr' => [
                    'class' => 'd-flex justify-content-center col-md-8'
                ]*/
            ])
            ->add('type', TextType::class, [
                'label' => 'Type d\'évènement :',/*
                'label_attr' => [
                    'class' => 'col-md-4'
                ],
                'row_attr' => [
                    'class' => 'd-flex justify-content-center col-md-8'
                ]*/
            ])
            ->add('dates', TextType::class, [
                'label' => 'Dates :',/*
                'label_attr' => [
                    'class' => 'col-md-4'
                ],
                'row_attr' => [
                    'class' => 'd-flex justify-content-center col-md-8'
                ]*/
            ])
            ->add('homeText', TextareaType::class, [
                'label' => 'Text d\'accueil :',
                'attr' => [
                    'rows' => 10
                ],/*
                'label_attr' => [
                    'class' => 'col-md-4'
                ],
                'row_attr' => [
                    'class' => 'd-flex justify-content-center col-md-8'
                ]*/
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Edition::class,
        ]);
    }
}
