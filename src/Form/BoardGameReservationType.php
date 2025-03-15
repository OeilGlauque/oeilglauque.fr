<?php
namespace App\Form;

use App\Entity\BoardGame;
use App\Entity\BoardGameReservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BoardGameReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateBeg', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de Début'
            ])

            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
            ])

            ->add('boardGames', EntityType::class, [
                'label' => 'Jeux',
                'class' => BoardGame::class,
                'multiple' => true,
                'expanded' => false,
                'choice_value' => function(BoardGame $boardGame) {
                    return $boardGame->getName();
                },
                'extra_options'=> function (BoardGame $boardGame) {
                    return [$boardGame->getPrice()];
                },
                'choice_label' => function (BoardGame $boardGame) {
                    $state = empty($boardGame->getState()) ? 'état inconnu' : 'Etat ' . $boardGame->getState();
                    $stateMappings = [
                        "Excellent" => "excellent_state",
                        "Bon" => "good_state",
                        "Moyen" => "medium_state",
                        "Mauvais" => "bad_state"
                    ];
                    $stateClass = $stateMappings[$boardGame->getState()] ?? "unknown_state";
                    return $boardGame->getName().'-'.$state.'-'.$stateClass.'-'.$boardGame->getPrice();
                },
                'attr' => [
                    'class' => 'tom-select',
                    'placeholder' => 'Ajouter un jeu...',
                ],
                'choices' => $options['choices'],
            ])

            ->add('note', TextareaType::class, ['label' => 'Note', 'required' => false, "empty_data"=>""])

            ->add('save', SubmitType::class, ['label' => 'Valider']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BoardGameReservation::class,
            'choices' => []
        ]);
    }
}