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
            ->select('c','p')
            ->join('p.categorie', 'c')
            ->where('c.proprietaire = :prop')
            ->setParameter('prop', $user->getProprietaire());
        if(!empty($search->categories))
        {
            $query = $query
                ->andWhere('p.categorie IN (:categories)')
                ->setParameter('categories', $search->categories);
        }    

        return $query->getQuery()->getResult();    
    }
     public function search($crit)
     {
        return $this->createQueryBuilder("p")->where('p.nom LIKE :crit')
                                                ->setParameter('crit',$crit.'%')
                                                ->getQuery()
                                                ->getResult();


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
}
