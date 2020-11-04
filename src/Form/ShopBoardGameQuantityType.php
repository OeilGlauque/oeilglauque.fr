<?php
namespace App\Form;

use App\Entity\ShopBoardGame;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopBoardGameQuantityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class, array('label' => 'Quantité'))

            ->add('boardGames', EntityType::class, array(
                'class' => ShopBoardGame::class,
                'label' => 'Jeux',
                'choice_label' => 'name',
                'choice_value' => 'id' 
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
?>