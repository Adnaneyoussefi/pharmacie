<?php

namespace App\Repository;

use App\Entity\User;
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

    public function GetNnVente(UserInterface $user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT count(D.id)
            FROM App\Entity\DetailsCommande D
            INNER JOIN D.produit p
            WHERE p.proprietaire = :prop'
              )->setParameter('prop', $user->getProprietaire());
        return $query->getSingleScalarResult();
    }

    public function findPrixTotal(UserInterface $user)
    {
        $query = $this
            ->createQueryBuilder('d')
            ->join('d.produit', 'p')
            ->leftjoin('d.commande', 'c')
            ->where('p.proprietaire = :prop')
            ->andWhere('d.livraison = :livr')
            ->groupBy('d.commande')
            ->setParameter('prop', $user->getProprietaire())
            ->select('SUM((p.prix_tva + p.prix_ht) * d.quantite) as x','c','d')
            ->setParameter('livr', 'oui');
        return $query->getQuery()->getScalarResult();
    }

    public function findPrixTotalClient(User $user)
    {
        $query = $this
            ->createQueryBuilder('d')
            ->join('d.produit', 'p')
            ->join('d.commande', 'c')
            ->where('c.client = :client')
            ->groupBy('d.commande')
            ->setParameter('client', $user->getClient())
            ->select('SUM((p.prix_tva + p.prix_ht) * d.quantite) as x','c','d');
        return $query->getQuery()->getScalarResult();
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
