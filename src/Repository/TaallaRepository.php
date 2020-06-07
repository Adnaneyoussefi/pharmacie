<?php

namespace App\Repository;

use App\Entity\Taalla;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taalla|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taalla|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taalla[]    findAll()
 * @method Taalla[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaallaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taalla::class);
    }

    // /**
    //  * @return Taalla[] Returns an array of Taalla objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Taalla
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
