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
            /*->add('annee', ChoiceType::class, [
                'label' => 'Année :',
                'choices' => range((int)date("Y"),(int)date("Y")+5),
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                }
            ])*/
            ->add('start', null, [
                'label' => 'Début',
                'widget' => 'single_text',
            ])
            ->add('end', null, [
                'label' => 'Fin',
                'widget' => 'single_text',
            ])
            ->add('type', TextType::class, [
                'label' => 'Type d\'évènement :',
            ])
            ->add('homeText', TextareaType::class, [
                'label' => 'Text d\'accueil :',
                'attr' => [
                    'rows' => 10
                ],
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
