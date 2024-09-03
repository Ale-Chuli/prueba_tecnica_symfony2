<?php

namespace App\Repository;

use App\Entity\Meditions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meditions>
 *
 * @method Meditions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meditions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meditions[]    findAll()
 * @method Meditions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeditionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meditions::class);
    }

    //Returns all meditions with the respect wine name
    public function findAllWithWineName(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m, v.name as wine_Name')
            ->innerJoin('App\Entity\Wines', 'v', 'WITH', 'm.wine = v.id')
            ->getQuery()
            ->getResult();
    }

}
