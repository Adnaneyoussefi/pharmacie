<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PharmaChangeInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_pharmacie')
            ->add('adresse_pharmacie')
            ->add('ville', ChoiceType::class, [
                'choices'  => [
                    'Casablanca' => 'Casablanca',
                    'Fès' => 'Fès',
                    'Salé' => 'Salé',
                    'Tanger' => 'Tanger',
                    'Marrakech' => 'Marrakech',
                    'Meknès' => 'Meknès',
                    'Rabat' => 'Rabat',
                    'Oujda' => 'Oujda',
                    'Kénitra' => 'Kénitra',
                    'Agadir' => 'Agadir',
                    'Tétouan' => 'Tétouan',
                    'Témara' => 'Témara',
                    'Safi' => 'Safi',
                    'Mohammédia' => 'Mohammédia',
                    'Khouribga' => 'Khouribga',
                    'El Jadida' => 'El Jadida',
                    'Béni Mellal' => 'Béni Mellal',
                    'Nador' => 'Nador',
                    'Taza' => 'Taza',
                    'Khémisset' => 'Khémisset',
                    'Autre...' => 'Autre',
                ],
            ])
            ->add('tel')
            ->add('Enregistrer', SubmitType::class, array (
                'attr'=>array(
                    'class'=>'btn btn-primary bnt-block'
                )
                ));

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
