<?php
namespace App\Form;

use App\Entity\Game;
use App\Entity\GameSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('gameSlot', EntityType::class, [
                'class' => GameSlot::class, 
                'choice_label' => 'text', 
                'label' => 'Créneau',
                'choices' => $options['slots'], 
                'required' => true,
            ])
            ->add('seats', IntegerType::class, ['label' => 'Places disponibles', 'invalid_message' => "Veuillez entrer un nombre"])
            ->add('tags', TextType::class, ['label' => 'Tags', 'required' => false])
            ->add('forceOnlineSeats', CheckboxType::class, ['label' => 'Permettre de réserver toutes les places en ligne (déconseillé). Par défaut, la moitié des places sont réservable en ligne et l\'autre moitié réservable sur place.', 'required' => false])
            
            ->add('save', SubmitType::class, ['label' => 'Valider']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'slots' => []
        ]);
    }
}
?>
