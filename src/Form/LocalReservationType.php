<?php
namespace App\Form;

use App\Entity\LocalReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
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
            ->add('date', DateTimeType::class,
                [
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'html5' => true,
                    'years' => [date("Y"), date("Y")+1]
                ])

            ->add('duration',IntegerType::class,array('label' => 'Durée (minutes)',
                'attr' => [
                    'min'=>'15',
                    'max'=>'300',
                    'minMessage' => 'La durée doit être supérieur à 15 minutes.',
                    'maxMessage' => 'La durée ne doit pas excéder 5h soit 300 minutes.']))

            ->add('motif', TextareaType::class, array('label' => 'Motif'))

            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}