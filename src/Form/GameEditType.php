<?php
namespace App\Form;

use App\Entity\Game;
use App\Entity\GameSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class GameEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description (max 1 000 caractères)', 'attr' => [
                'maxlength' => 1000,
            ]])
            ->add('gameSlot', EntityType::class, [
                'class' => GameSlot::class, 
                'choice_label' => 'text', 
                'label' => 'Créneau', 
                'disabled' => true, 
                'choices' => $options['slots'], 
            ])
            ->add('seats', IntegerType::class, ['label' => 'Places disponibles', 'attr' => ['min' => $options['seats']],'invalid_message' => "Veuillez entrer un nombre"])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'autocomplete' => true,
                'data' => $options['tags'],
                'choices' => [
                    'Cyberpunk' => 'cyberpunk',
                    'Débutants' => 'débutants',
                    'Enfants' => 'enfants',
                    'Enquête' => 'enquête',
                    'Escape game' => 'escape game',
                    'Exploration' => 'exploration',
                    'Historique' => 'Historique',
                    'Humour' => 'humour',
                    'Horreur' => 'horreur',
                    'Magie' => 'magie',
                    'Manga' => 'manga',
                    'Médiéval' => 'médiéval',
                    'Murder' => 'murder',
                    'Post-apocalyptique' => 'post-apocalyptique',
                    'SF' => 'SF',
                    'Sombre' => 'sombre',
                    'Surnaturel' => 'surnaturel',
                    'Voyage' => 'voyage'
                ],
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['style' => 'height: 200px', 'class' => 'tom-select', 'placeholder' => 'Choisissez des tags...'],
            ])            ->add('tw', TextType::class, ['label' => 'TW', 'required' => false])
            ->add('forceOnlineSeats', CheckboxType::class, ['label' => 'Permettre de réserver toutes les places en ligne (déconseillé). Par défaut, la moitié des places sont réservable en ligne et l\'autre moitié réservable sur place.', 'required' => false])
            ->add('img', FileType::class, [
                'label' => "Image (optionel)",
                'mapped' => false,
                'required' => false,
                'constraints' => new File([
                    'maxSize' => '2M',
                    'maxSizeMessage' => "Le fichier est trop lourd ({{ size }} {{ suffix }}), la taille maximale est de 2 Mo.",
                    'uploadIniSizeErrorMessage' => "Le fichier est trop lourd, la taille maximale est de 2Mo.",
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => "Le type de fichier n'est pas valide. Les formats acceptés sont png, jpeg et webp."
                ])
            ])
            
            ->add('save', SubmitType::class, ['label' => 'Mettre à jour']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'slots' => [],
            'seats' => 1,
            'tags' => []
        ]);
    }
}