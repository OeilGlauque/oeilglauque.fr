<?php
namespace App\Form;

use App\Entity\Game;
use App\Entity\GameSlot;
use App\Entity\LocalReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gameSlot', EntityType::class, array(
                'class' => GameSlot::class,
                'choice_label' => 'text',
                'label' => 'Créneau',
                'choices' => $options['slots'],
            ))
            ->add('note', TextareaType::class, array('label' => 'Note'))

            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LocalReservation::class,
            'slots' => array()
        ));
    }
}
?>