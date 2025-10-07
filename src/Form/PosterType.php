<?php

namespace App\Form;

use App\Entity\Edition;
use App\Entity\Poster;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PosterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('edition', null, [
                'class' => Edition::class,
                'choice_label' => 'annee',
                'label' => 'Edition (optionnel)',
                'choices' => $options['editions'],
                'data' => $options['edition'],
                'required' => false,
            ])
            ->add('title', null, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'FOG XX...',
                ],
            ])
            ->add('authors', null, [
                'label' => 'Auteurs',
                'attr' => [
                    'placeholder' => 'Perceneige - Aurélien - ...',
                ],
            ])
            ->add('fogImg', FileType::class, [
                'label' => "Image Fog (.webp)",
                'mapped' => false,
                'required' => $options['new'],
                'constraints' => new File([
                    'maxSize' => '2M',
                    'maxSizeMessage' => "(Image FOG) Le fichier est trop lourd ({{ size }} {{ suffix }}), la taille maximale est de 2 Mo.",
                    'uploadIniSizeErrorMessage' => "(Image FOG) Le fichier est trop lourd, la taille maximale est de 2Mo.",
                    'mimeTypes' => [
                        'image/webp'
                    ],
                    'mimeTypesMessage' => "(Image FOG) Veuillez fournir un fichier .webp"
                ])
            ])
            ->add('concertImg', FileType::class, [
                'label' => "Image Concert (.webp) (optionnel)",
                'mapped' => false,
                'required' => false,
                'constraints' => new File([
                    'maxSize' => '2M',
                    'maxSizeMessage' => "(Concert Fog) Le fichier est trop lourd ({{ size }} {{ suffix }}), la taille maximale est de 2 Mo.",
                    'uploadIniSizeErrorMessage' => "(Concert Fog) Le fichier est trop lourd, la taille maximale est de 2Mo.",
                    'mimeTypes' => [
                        'image/webp'
                    ],
                    'mimeTypesMessage' => "(Concert Fog) Veuillez fournir un fichier .webp"
                ])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poster::class,
            'new' => true,
            'editions' => [],
            'edition' => null,
        ]);
    }
}