<?php

namespace App\Repository;

use App\Entity\User;
use App\Data\SearchData;
use App\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findCommande(UserInterface $user)
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('d','c')
            ->join('c.produits', 'd')
            ->join('d.produit', 'p')
            ->where('p.proprietaire = :prop')
            ->andwhere('d.livraison = :encours  OR d.livraison IS NULL')
            ->orderBy('c.date', 'DESC')
            ->setParameter('prop', $user->getProprietaire())
            ->setParameter('encours', 'encours');
        return $query->getQuery()->getResult();    
    }

    public function findCmdClient(User $user)
    {
        $query = $this
            ->createQueryBuilder('c')
            ->join('c.produits', 'd')
            ->join('d.produit', 'p')
            ->where('c.client = :client')
            ->orderBy('c.date', 'DESC')
            ->setParameter('client', $user->getClient());
        return $query->getQuery()->getResult();    
    }

    public function findVentes(SearchData $search, UserInterface $user)
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('d','c')
            ->join('c.produits', 'd')
            ->join('d.produit', 'p')
            ->where('p.proprietaire = :prop')
            ->andWhere('d.livraison = :livr')
            ->orderBy('c.date', 'DESC')
            ->setParameter('prop', $user->getProprietaire())
            ->setParameter('livr', 'oui');
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

    // /**
    //  * @return Commande[] Returns an array of Commande objects
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
    public function findOneBySomeField($value): ?Commande
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
