<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('beginAt', null, [
                'label' => 'Début',
                'widget' => 'single_text',
            ])
            ->add('endAt', null, [
                'label' => 'Fin',
                'widget' => 'single_text',
            ])
            ->add('title', null, [
                'label' => 'Titre',
            ])
            ->add('location', null, [
                'label' => 'Lieu',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
