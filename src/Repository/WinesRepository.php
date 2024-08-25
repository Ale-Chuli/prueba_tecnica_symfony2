<?php

namespace App\Repository;

use App\Entity\Wines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wines>
 *
 * @method Wines|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wines|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wines[]    findAll()
 * @method Wines[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WinesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wines::class);
    }

//    /**
//     * @return Wines[] Returns an array of Wines objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }


//    public function findOneBySomeField($value): ?Wines
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
