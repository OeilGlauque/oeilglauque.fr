<?php
namespace App\Form;

use App\Entity\Game;
use App\Entity\GameSlot;
use App\Entity\LocalReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('date', DateTimeType::class, array(
                'label' => 'Créneau',
                'years' => [date("Y"), date("Y")+1],
                /*'widget' => 'single_text',
                'format' => 'dd/MM/yyyy hh:mm',
                'html5' => true,
                'type' => 'datetime',
                'placeholder' => '01/01/2020 15:30'*/))
            ->add('duration',NumberType::class,array('label' => 'Durée (minutes)'))
            ->add('note', TextareaType::class, array('label' => 'Note'))
            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LocalReservation::class));
    }
}
?>