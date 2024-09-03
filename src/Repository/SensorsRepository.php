<?php

namespace App\Repository;

use App\Entity\Sensors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sensors>
 *
 * @method Sensors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensors[]    findAll()
 * @method Sensors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensors::class);
    }

    //Returns all sensors ordered by name in ascending order
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
