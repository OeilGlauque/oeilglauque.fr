<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail', 
                'invalid_message' => 'Veuillez fournir une adresse mail valide',
                'constraints' => [new Assert\NotBlank()],
                'data' => $options['user']->getEmail()
            ])
            ->add('name', TextType::class, ['label' => 'Nom', 'data' => $options['user']->getName(), 'constraints' => [new Assert\NotBlank()]])
            ->add('firstname', TextType::class, ['label' => 'Prénom', 'data' => $options['user']->getFirstName(), 'constraints' => [new Assert\NotBlank()]])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'help' => 'Laissez vide pour ne pas modifier', 
                'first_options'  => ['label' => 'Nouveau mot de passe (laissez vide pour ne pas modifier)'],
                'second_options' => ['label' => 'Nouveau mot de passe (confirmation)'],
                'required' => false, 
            ])
            ->add('save', SubmitType::class, ['label' => 'Mise à jour']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => null
        ]);
    }
}