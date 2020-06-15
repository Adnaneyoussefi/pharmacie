<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }
    public function OrientaleCli()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT count(c.id)
            FROM App\Entity\Client c
            WHERE c.region = :region'
              )->setParameter('region', 'Oriental');
        return $query->getSingleScalarResult();
    }
    public function TangertetouanAlhoceimaCli()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(c.id)
                    FROM App\Entity\Client c
                    WHERE c.region = :region'
                    )->setParameter('region', 'Tanger-Tétouan-Al Hoceïma');
                return $query->getSingleScalarResult();
            }
            public function FesMeknesCli()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(c.id)
                    FROM App\Entity\Client c
                    WHERE c.region = :region'
                    )->setParameter('region', 'Fès-Meknès');
                return $query->getSingleScalarResult();
            }
            public function CasablancaSettatCli()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(c.id)
                    FROM App\Entity\Client c
                    WHERE c.region = :region'
                    )->setParameter('region', 'Casablanca-Settat');
                return $query->getSingleScalarResult();
            }
            public function RabatSaleKenitraCli()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(c.id)
                    FROM App\Entity\Client c
                    WHERE c.region = :region'
                    )->setParameter('region', 'Rabat-Salé-Kénitra');
                return $query->getSingleScalarResult();
            }

    // /**
    //  * @return Client[] Returns an array of Client objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
