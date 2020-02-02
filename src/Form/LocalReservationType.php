<?php
namespace App\Form;

use App\Entity\LocalReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LocalReservationType extends AbstractType
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
            // TODO: Better way to pick date & time

            ->add('duration',IntegerType::class,array('label' => 'Durée (minutes)', 'invalid_message' => "Veuillez entrer un nombre",
                'min' => 15, 'max' => 300, ))
            ->add('motif', TextareaType::class, array('label' => 'Motif'))
            ->add('save', SubmitType::class, array('label' => 'Valider'));
        // TODO: Work on margins
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LocalReservation::class));
    }
}
?>