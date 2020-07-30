<?php
namespace App\Form;

use App\Entity\BoardGameReservation;
use App\Entity\LocalReservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BoardGameReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class,
                ['widget' => 'single_text',
                    'html5' => true,
                    'years' => [date("Y"), date("Y")+1]])

            ->add('time', TimeType::class,
                ['widget' => 'single_text',
                'label' => 'Heure',
                'html5' => true,
                'invalid_message' => 'L\'heure doit être rentrée en incrément de 15 minutes',
                'attr' => ['step' => 900]])

            ->add('duration',IntegerType::class,array('label' => 'Durée (minutes)',
                'invalid_message' => 'La durée doit être rentrée en incrément de 15 minutes',
                'attr' => ['min'=>'15', 'max'=>'300', 'step'=>'15']))

            ->add('boardGames', EntityType::class, array(
                'label'=>'Jeux',
                'class' => 'App\Entity\BoardGame',
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'select2multiple'/*, 'multiple'=> 'multiple'*/]))

            ->add('note', TextareaType::class, array('label' => 'Note'))

            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BoardGameReservation::class));
    }
}
?>