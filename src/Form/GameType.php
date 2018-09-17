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
            ->add('title', TextType::class, array('label' => 'Titre'))
            ->add('description', TextareaType::class, array('label' => 'Description'))
            ->add('gameSlot', EntityType::class, array(
                'class' => GameSlot::class, 
                'choice_label' => 'text', 
                'label' => 'Créneau',
                'choices' => $options['slots'], 
            ))
            ->add('seats', IntegerType::class, array('label' => 'Places disponibles', 'invalid_message' => "Veuillez entrer un nombre"))
            ->add('forceOnlineSeats', CheckboxType::class, array('label' => 'Permettre de réserver toutes les places en ligne (déconseillé)', 'required' => false))
            
            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Game::class,
            'slots' => array()
        ));
    }
}
?>