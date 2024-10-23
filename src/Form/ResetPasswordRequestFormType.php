<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                    'autofocus' => true
                ],
                'label' => 'Adresse mail',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre adresse mail',
                    ]),
                ],
                'invalid_message' => 'Veuillez fournir une adresse mail valide'
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-lg btn-primary'
                ],
                'label' => 'Réinitialiser mon mot de passe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
