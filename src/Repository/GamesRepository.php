<?php

namespace App\Repository;

use App\Entity\Games;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Games|null find($id, $lockMode = null, $lockVersion = null)
 * @method Games|null findOneBy(array $criteria, array $orderBy = null)
 * @method Games[]    findAll()
 * @method Games[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Games::class);
    }

    public function search($value)
    {
        return $this->createQueryBuilder('s')
            ->where("s.title LIKE '%" . $value . "%'")
            ->getQuery()
            ->getResult();
    }

    public function searchType($value)
    {
        return $this->createQueryBuilder('s')
            ->where("s.genres LIKE '%" . $value . "%'")
            ->getQuery()
            ->getResult();
    }

    public function findGames($page)
    {
        $first = 1;
        $second = 20;

        if ($page > 1) {
            $first += (20 * ($page - 1));
            $second += (20 * ($page - 1));
        }
        return $this->createQueryBuilder('g')
            ->where('g.id >= ?1')
            ->andWhere('g.id <= ?2')
            ->setParameter(1, $first)
            ->setParameter(2, $second)
            ->getQuery()
            ->getResult();
    }

    public function search10LastGames() {
        return $this->createQueryBuilder('t')
            ->orderBy("t.releaseYear", "desc")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function searchEsport()
    {
        return $this->createQueryBuilder('e')
            ->where("e.id = 1213")
            ->getQuery()
            ->getResult();
    }

    public function findGamesByString($str){
        return $this->createQueryBuilder('g')
            ->where('g.title LIKE :str')
            ->setParameter('str', '%'.$str.'%')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Games[] Returns an array of Games objects
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
    public function findOneBySomeField($value): ?Games
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
