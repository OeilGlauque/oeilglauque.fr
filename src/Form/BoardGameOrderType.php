<?php
namespace App\Form;

use App\Entity\BoardGameOrder;
use App\Form\ShopBoardGameQuantityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

            ->add('boardGamesQuantity', CollectionType::class, array(
                'entry_type' => ShopBoardGameQuantityType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
            ))

            ->add('save', SubmitType::class, array('label' => 'Valider'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
?>