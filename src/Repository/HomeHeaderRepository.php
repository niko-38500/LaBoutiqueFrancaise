<?php

namespace App\Repository;

use App\Entity\HomeHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HomeHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeHeader[]    findAll()
 * @method HomeHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeHeader::class);
    }

    // /**
    //  * @return HomeHeader[] Returns an array of HomeHeader objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HomeHeader
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
