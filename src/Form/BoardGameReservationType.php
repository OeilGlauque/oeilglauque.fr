<?php
namespace App\Form;

use App\Entity\BoardGame;
use App\Entity\BoardGameReservation;
use App\Repository\BoardGameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BoardGameReservationType extends AbstractType
{

    private $boardGamesRepository;

    public function __construct(BoardGameRepository $boardGamesRepository){
        $this->boardGamesRepository = $boardGamesRepository;
    }

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
                'label'=>'Jeux',
                'class' => 'App\Entity\BoardGame',
                'label_html' => true,
                'choice_label' => function (BoardGame $boardGame) {
                    $state = empty($boardGame->getState()) ? 'état inconnu' : 'Etat ' . $boardGame->getState();

                    $stateMappings = [
                        "Excellent" => "excellent_state",
                        "Bon" => "good_state",
                        "Moyen" => "medium_state",
                        "Mauvais" => "bad_state"
                    ];

                    $stateClass = $stateMappings[$boardGame->getState()] ?? "unknown_state";

                    return '<div class="game_container"><div>' . $boardGame->getName() . '</div> <div class="game_state '. $stateClass .'">' . $state . '</div> <div>' . $boardGame->getPrice() . '€</div></div>';
                },
                'multiple' => true,
                'choices' => $this->boardGamesRepository->findAllAlphabetical(),
                'expanded' => true,
                'attr' => ['style' => 'height: 150px']  /*'select2multiple', 'multiple'=> 'multiple'*/
            ])

            ->add('note', TextareaType::class, ['label' => 'Note', 'required' => false, "empty_data"=>""])

            ->add('save', SubmitType::class, ['label' => 'Valider']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BoardGameReservation::class));
    }
}