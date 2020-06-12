<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\DetailsCommande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method DetailsCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsCommande[]    findAll()
 * @method DetailsCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsCommande::class);
    }

    public function findVentes(SearchData $search, UserInterface $user)
    {
        $query = $this
            ->createQueryBuilder('d')
            ->select('p','d')
            ->join('d.produit', 'p')
            ->where('p.proprietaire = :prop')
            ->setParameter('prop', $user->getProprietaire());
        if(!empty($search->min))
        {
            $query = $query
                ->andWhere('(p.prix_tva + p.prix_ht) * d.quantite >= :min')
                ->setParameter('min', $search->min);
        }
        if(!empty($search->max))
        {
            $query = $query
                ->andWhere('(p.prix_tva + p.prix_ht) * d.quantite <= :max')
                ->setParameter('max', $search->max);
        }    
        return $query->getQuery()->getResult();    
    }
    public function GetNnVente(UserInterface $user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT count(D.id)
            FROM App\Entity\DetailsCommande D
            INNER JOIN App\Entity\produit p
            WHERE p.proprietaire = :prop'
              )->setParameter('prop', $user->getProprietaire());
        return $query->getSingleScalarResult();
    }
    // /**
    //  * @return DetailsCommande[] Returns an array of DetailsCommande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DetailsCommande
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
