<?php

namespace App\Repository;

use App\Entity\Garde;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Garde|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garde|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garde[]    findAll()
 * @method Garde[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GardeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garde::class);
    }

    // /**
    //  * @return Garde[] Returns an array of Garde objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Garde
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
