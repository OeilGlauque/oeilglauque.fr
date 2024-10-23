<?php

namespace App\Repository;

use App\Entity\BoardGameReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

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
            ->where('r.dateEnd > :yesterday')
            ->setParameter('yesterday', new \DateTime('-1 day'), Types::DATETIME_MUTABLE)
            ->addOrderBy('r.dateBeg', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return BoardGameReservation[]
     */
    public function getBoardGameReservationArchive(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.dateEnd < :today')
            ->andWhere('r.dateEnd > :lastyear')
            ->setParameter('today', new \DateTime(), Types::DATETIME_MUTABLE)
            ->setParameter('lastyear', new \DateTime('-1 year'), Types::DATETIME_MUTABLE)
            ->addOrderBy('r.dateBeg', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param BoardGameReservation $reservation
     * @return int
     */
    public function findBoardGameReservationOverlap(BoardGameReservation $reservation): array
    {
        $qb = $this->createQueryBuilder('r');

        $c1 = $qb->expr()->andX(
            $qb->expr()->lte('r.dateBeg', ':begDate'),
            $qb->expr()->gte('r.dateEnd', ':begDate')
        );

        $c2 = $qb->expr()->andX(
            $qb->expr()->lte('r.dateBeg', ':endDate'),
            $qb->expr()->gte('r.dateEnd', ':endDate')
        );

        $c3 = $qb->expr()->andX(
            $qb->expr()->gte('r.dateBeg', ':begDate'),
            $qb->expr()->lte('r.dateEnd', ':endDate')
        );

        $qb->where($qb->expr()->orX($c1, $c2, $c3))
            ->setParameter('begDate', $reservation->getDateBeg(), Types::DATETIME_MUTABLE)
            ->setParameter('endDate', $reservation->getDateEnd(), Types::DATETIME_MUTABLE);

        $res = $qb->getQuery()->getResult();
        $ret = [];

        foreach($res as $l) {
            $ret = array_merge($ret, $l->getBoardGames()->toArray());
        }

        return array_unique(array_intersect($ret, $reservation->getBoardGames()->toArray()));
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
