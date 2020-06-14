<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SearchForm extends AbstractType
{
    private $security;

        public function __construct(Security $security)
        {
            $this->security = $security;
        }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'label' => false,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                    ->select('p','c')
                    ->join('c.produits', 'p')
                    ->where('p.proprietaire = :prop')
                    ->setParameter('prop', $this->security->getUser()->getProprietaire());
                },
                'choice_label' => function(Categorie $cat) {
                    return $cat->getNom()." (". $cat->getProduits()->count().")"; 
                },
            ])
            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Min'
                ]
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Max'
                ]
            ])
            ->add('expire', CheckboxType::class, [
                'label' => 'Produits expirés',
                'required' => false,
            ])
            ->add('epuise', CheckboxType::class, [
                'label' => 'Produits épuisés',
                'required' => false,
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
    
}