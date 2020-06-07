<?php

namespace App\Repository;

use App\Entity\Code;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Code|null find($id, $lockMode = null, $lockVersion = null)
 * @method Code|null findOneBy(array $criteria, array $orderBy = null)
 * @method Code[]    findAll()
 * @method Code[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Code::class);
    }

    public function LastWeekOrders() {
        $date = date('Y-m-d h:i:s', strtotime("-7 days"));

        return $this->createQueryBuilder('o')
            ->where('o.purchaseDate BETWEEN :n7days AND :today')
            ->setParameter('today', date('Y-m-d h:i:s'))
            ->setParameter('n7days', $date)
            ->getQuery()
            ->getArrayResult();
    }

    public function LastWeekPurchases() {
        $date = date('Y-m-d h:i:s', strtotime("-7 days"));

        return $this->createQueryBuilder('p')
            ->where('p.purchaseDate BETWEEN :n7days AND :today')
            ->setParameter('today', date('Y-m-d h:i:s'))
            ->setParameter('n7days', $date)
            ->getQuery()
            ->getArrayResult();
    }

    public function search10LastOrder()
    {
        return $this->createQueryBuilder('o')
            ->orderBy("o.purchaseDate", "desc")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Code[] Returns an array of Code objects
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
    public function findOneBySomeField($value): ?Code
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
