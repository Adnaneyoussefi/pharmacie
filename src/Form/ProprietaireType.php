<?php

namespace App\Form;

use App\Entity\Proprietaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProprietaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_pharmacie')
            ->add('adresse')
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
            ->add('region', ChoiceType::class, [
                'choices'  => [
                    'Tanger-Tétouan-Al Hoceïma' => 'Tanger-Tétouan-Al Hoceïma',
                    'Oriental' => 'Oriental',
                    'Fès-Meknès' => 'Fès-Meknès',
                    'Rabat-Salé-Kénitra' => 'Rabat-Salé-Kénitra',
                    'Béni Mellal-Khénifra' => 'Béni Mellal-Khénifra',
                    'Casablanca-Settat' => 'Casablanca-Settat',
                    'Marrakech-Safi' => 'Marrakech-Safi',
                    'Drâa-Tafilalet' => 'Drâa-Tafilalet',
                    'Souss-Massa' => 'Souss-Massa',
                    'Guelmim-Oued Noun' => 'Guelmim-Oued Noun',
                    'Laâyoune-Sakia El Hamra' => 'Laâyoune-Sakia El Hamra',
                    'Dakhla-Oued Ed-Dahab' => 'Dakhla-Oued Ed-Dahab',
                ],
            ])
            ->add('tel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proprietaire::class,
        ]);
    }
}
