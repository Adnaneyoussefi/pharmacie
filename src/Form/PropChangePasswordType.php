<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PropChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldpassword', PasswordType::class, array (
                'mapped'=>false
            ))
            ->add('confirmpassword', RepeatedType::class,[
                'constraints' => [
                    new Length([
                    'min' => 8,
                    'minMessage' => 'Votre mot de passe doit faire minimum 8 caractères'
                ])
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Nouveau mot de passe', 'attr' => ['placeholder' => 'Ecrire le nouveau mot de passe']],
                'second_options' => ['label' => 'confirmation du nouveau mot de passe', 'attr' => ['placeholder' => 'confirmer le mot de passe']],              
            ])
            ->add('Enregistrer', SubmitType::class, array (
                'attr'=>array(
                    'class'=>'btn btn-primary bnt-block'
                )
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
