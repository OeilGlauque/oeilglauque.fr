<?php
namespace App\Form;

use App\Entity\BoardGameOrder;
use App\Entity\ShopBoardGame;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoardGameOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Prénom'))

            ->add('surname', TextType::class, array('label' => 'Nom'))

            ->add('mail', EmailType::class, array('label'=>'Adresse mail'))

            ->add('boardGames', EntityType::class, array(
                'class' => 'App\Entity\ShopBoardGame', 
                'choice_label' => 'name', 
                'label' => 'Jeux',
                'multiple' => true,
                'attr' => ['class' => 'select2multiple']
            ))

            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            // 'data_class' => BoardGameOrder::class
        ));
    }
}
?>