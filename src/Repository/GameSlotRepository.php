<?php

namespace App\Repository;

use App\Entity\GameSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameSlot[]    findAll()
 * @method GameSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSlot::class);
    }

//    /**
//     * @return GameSlot[] Returns an array of GameSlot objects
//     */
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
    public function findOneBySomeField($value): ?GameSlot
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
