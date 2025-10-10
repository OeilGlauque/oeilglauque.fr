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

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'disabled' => $options['slots']->isEmpty(),
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (max 1 000 caractères)',
                'attr' => [
                    'maxlength' => 1000,
                ],
                'disabled' => $options['slots']->isEmpty(),
            ])
            ->add('gameSlot', EntityType::class, [
                'class' => GameSlot::class,
                'choice_label' => function ($slot) {
                    return $slot->getText() . ' (Places restantes: ' . ($slot->getMaxGames() - count($slot->getGames())) . ')';
                },
                'label' => 'Créneau',
                'choices' => $options['slots'], 
                'required' => true,
                'disabled' => $options['slots']->isEmpty(),
            ])
            ->add('seats', IntegerType::class, [
                'label' => 'Places disponibles',
                'invalid_message' => "Veuillez entrer un nombre",
                'disabled' => $options['slots']->isEmpty(),
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'autocomplete' => true,
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
                'disabled' => $options['slots']->isEmpty(),
                'attr' => ['style' => 'height: 200px', 'class' => 'tom-select', 'placeholder' => 'Choisissez des tags...'],
            ])
            ->add('tw', TextType::class, [
                'label' => 'Trigger Warning (Avertissements)',
                'required' => false,
                'attr' => array('placeholder' => 'Gore, déconseillé aux enfants, suicide, ...',),
                'disabled' => $options['slots']->isEmpty(),
                ])
            ->add('forceOnlineSeats', CheckboxType::class, [
                'label' => 'Permettre de réserver toutes les places en ligne (déconseillé). Par défaut, la moitié des places sont réservable en ligne et l\'autre moitié réservable sur place.',
                'required' => false,
                'disabled' => $options['slots']->isEmpty(),
            ])
            ->add('img', FileType::class, [
                'label' => "Image (optionel)",
                'mapped' => false,
                'disabled' => $options['slots']->isEmpty(),
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
            
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'disabled' => $options['slots']->isEmpty(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'slots' => [],
        ]);
    }
}