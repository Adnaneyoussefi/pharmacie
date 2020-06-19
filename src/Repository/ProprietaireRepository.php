<?php

namespace App\Repository;

use App\Entity\Proprietaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Proprietaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proprietaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proprietaire[]    findAll()
 * @method Proprietaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProprietaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proprietaire::class);
    }
            public function OrientaleProp()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(p.id)
                    FROM App\Entity\Proprietaire p
                    WHERE p.region=:region '
                    )->setParameter('region', 'oriental');
                return $query->getSingleScalarResult();
            }
            public function TangertetouanAlhoceimaProp()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(p.id)
                    FROM App\Entity\Proprietaire p
                    WHERE p.region=:region '
                    )->setParameter('region', 'Tanger-Tétouan-Al Hoceïma');
                return $query->getSingleScalarResult();
            }
            public function FesMeknesProp()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(p.id)
                    FROM App\Entity\Proprietaire p
                    WHERE p.region=:region '
                    )->setParameter('region', 'Fès-Meknès');
                return $query->getSingleScalarResult();
            }
            public function CasablancaSettatProp()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(p.id)
                    FROM App\Entity\Proprietaire p
                    WHERE p.region=:region '
                    )->setParameter('region', 'Casablanca-Settat');
                return $query->getSingleScalarResult();
            }
            public function RabatSaleKenitraProp()
            {
                $entityManager = $this->getEntityManager();

                $query = $entityManager->createQuery(
                    'SELECT count(p.id)
                    FROM App\Entity\Proprietaire p
                    WHERE p.region=:region '
                    )->setParameter('region', 'Rabat-Salé-Kénitra');
                return $query->getSingleScalarResult();
            }

    public function getLastPharmacie(){
        $entityManager = $this->getEntityManager();
   
        $query = $entityManager->createQuery(
            'SELECT p 
            FROM App\Entity\Proprietaire p 
            ORDER BY p.id DESC ')
            ->setMaxResults(6);
        return $query->getResult();
        }

        public function search($nom,$ville)
        {        
            $query = $this
                ->createQueryBuilder('p');
            if(!empty($nom))
            {
                $query = $query
                    ->andWhere('p.nom_pharmacie LIKE :nom')
                    ->setParameter('nom','%'.$nom.'%');
            }
            if(!empty($ville))
            {
                $query = $query
                ->andWhere('p.ville=:ville')
                ->setParameter('ville',$ville);
            }
            return $query->getQuery()->getResult();
        }





    // /**
    //  * @return Proprietaire[] Returns an array of Proprietaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Proprietaire
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
