<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    
    public function findByExampleField($value)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Product p
            '
        );

        // returns an array of Product objects
        return $query->getResult();
    }
    public function totalPages()
    {


        return $this->createQueryBuilder("p")->select('count(p.id)')->getQuery()->getSingleScalarResult();
    }
    public function FindByPage($offset,$max)
     {  
        return $this->getEntityManager()
        ->createQuery('SELECT p FROM App\Entity\Produit p')
        ->setMaxResults($max)
        ->setFirstResult($offset-$max)
        ->getResult();


     }

    public function findSearch(SearchData $search, UserInterface $user)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->where('p.proprietaire = :prop')
            ->orderBy('p.created_at', 'DESC')
            ->setParameter('prop', $user->getProprietaire());
        if(!empty($search->categories))
        {
            $query = $query
                ->andWhere('p.categorie IN (:categories)')
                ->setParameter('categories', $search->categories);
        }
        if(!empty($search->expire))
        {
            $query = $query
                ->andWhere("p.date_expiration <= CURRENT_DATE() ");
        }
        if(!empty($search->epuise))
        {
            $query = $query
                ->andWhere("p.quantite = 0 ");
        }
        return $query->getQuery()->getResult();    
    }
    public function getProducts()
    {
        return $this->createQueryBuilder('p')
                        ->where('p.date_expiration > CURRENT_DATE()')
                        ->getQuery()
                        ->getResult();
    }

    public function search($crit,$ville,$min,$max,$categorie)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->leftjoin('p.proprietaire','u')
            ->leftjoin('p.categorie','y')
            ->where('p.date_expiration > CURRENT_DATE()');
        if(!empty($crit))
        {
            $query = $query
                ->andWhere('p.nom LIKE :crit')
                ->setParameter('crit',$crit.'%');
        }
        if(!empty($ville))
        {
            $query = $query
            ->andWhere('u.ville=:ville')
            ->setParameter('ville',$ville);
        }
        if(!empty($min))
        {
            $query = $query
            ->andWhere('p.prix_tva >= :min')
            ->setParameter('min', $min);
        }
        if(!empty($max))
        {
            $query = $query
            ->andWhere('p.prix_tva<= :max')
            ->setParameter('max', $max);
        }
        if(!empty($categorie))
        {
           $query = $query
            ->andWhere('y.nom=:cat')
            ->setParameter('cat',$categorie);
           // dd($query->getQuery()->getResult());
        }
        return $query->getQuery()->getResult();
        /*if($ville=='all')
        {return $this->createQueryBuilder("p")->where('p.nom LIKE :crit')
                                                ->setParameter('crit',$crit.'%')
                                                ->getQuery()
                                                ->getResult();}
        else{

            return $this->createQueryBuilder("p")
            ->leftjoin('p.proprietaire','u')
            ->where('u.ville=:ville and p.nom LIKE :crit')
            ->setParameter('ville',$ville)
            ->setParameter('crit',$crit.'%')
            ->getQuery()
            ->getResult();       
        }        */                                
     }
     public function getLastProduct(){
     $entityManager = $this->getEntityManager();

     $query = $entityManager->createQuery(
         'SELECT p 
         FROM App\Entity\produit p 
         ORDER BY p.id DESC ')
         ->setMaxResults(6);
     return $query->getResult();
     }
    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getProductByCategorie($categorie)
    {
        return $this->createQueryBuilder("p")
        ->leftjoin('p.categorie','u')
        ->where('u.nom=:cat')
        ->setParameter('cat',$categorie)
        ->getQuery()
        ->getResult();     

    }
    public function GetNnProduit(UserInterface $user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT count(p.id)
            FROM App\Entity\Produit p
            WHERE p.proprietaire = :prop'
              )->setParameter('prop', $user->getProprietaire());
        return $query->getSingleScalarResult();
    }
}

