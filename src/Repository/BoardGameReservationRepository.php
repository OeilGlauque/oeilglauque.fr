<?php

namespace App\Repository;

use App\Entity\BoardGameReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoardGameReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardGameReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardGameReservation[]    findAll()
 * @method BoardGameReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardGameReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardGameReservation::class);
    }

    /**
     * @return BoardGameReservation[]
     */
    public function getBoardGameReservationList(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.date > :yesterday')
            ->setParameter('yesterday', new \DateTime('-1 day'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->addOrderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return BoardGameReservation[]
     */
    public function getBoardGameReservationArchive(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.date < :today')
            ->andWhere('r.date > :lastyear')
            ->setParameter('today', new \DateTime(), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('lastyear', new \DateTime('-1 year'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->addOrderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return BoardGameReservation[] Returns an array of BoardGameReservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BoardGameReservation
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
