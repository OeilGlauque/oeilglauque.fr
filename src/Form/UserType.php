<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Adresse mail', 'invalid_message' => 'Veuillez fournir une adresse mail valide'])
            ->add('pseudo', TextType::class, ['label' => 'Pseudo'])
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Mot de passe (confirmation)'],
                'constraints' =>[new Assert\Length(
                    min: 5,
                    max: 64,
                    minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    maxMessage: 'Votre mot de passe ne peut pas contenir plus de {{ limit }} caractères'
                )]
            ])
            ->add('save', SubmitType::class, ['label' => 'Inscription']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}