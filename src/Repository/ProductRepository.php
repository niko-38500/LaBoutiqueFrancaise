<?php

namespace App\Repository;

use App\Classes\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Requete qui permet de recupérer les produit avec une recherche
     * @return Product[]
     */
    public function findWithSearch(Search $search)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c')
            ;

            if (!empty($search->category) && !empty($search->string)) {
                $query = $query
                    ->Where('c.id IN (:category)')
                    ->andWhere('p.subtitle LIKE :string')
                    ->setParameter('category', $search->category)
                    ->setParameter('string', "%{$search->string}%")
                    ;
                    
                return $query->getQuery()->getResult();
            }

            if (!empty($search->category)) {
                $query = $query
                    ->andWhere('c.id IN (:category)')
                    ->setParameter('category', $search->category)
                    ;
            }

            if (!empty($search->string)) {
                $query = $query
                    ->andWhere('p.name LIKE :string')
                    ->orWhere('p.subtitle LIKE :string')
                    ->orWhere('p.description LIKE :string')
                    ->setParameter('string', "%{$search->string}%")
                    ;
            }

            return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
