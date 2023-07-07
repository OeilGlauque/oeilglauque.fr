<?php

namespace App\Repository;

use App\Entity\LocalReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Exception;
use Doctrine\Persistence\ManagerRegistry;

class LocalReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalReservation::class);

    }

    /**
     * @return LocalReservation[]
     */
    public function getLocalReservationList(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.date > :yesterday')
            ->setParameter('yesterday', new \DateTime('-1 day'), Types::DATETIME_MUTABLE)
            ->addOrderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return LocalReservation[]
     */
    public function getLocalReservationArchive(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.date < :today')
            ->andWhere('r.date > :lastyear')
            ->setParameter('today', new \DateTime(), Types::DATETIME_MUTABLE)
            ->setParameter('lastyear', new \DateTime('-1 year'), Types::DATETIME_MUTABLE)
            ->addOrderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param LocalReservation $reservation
     * @return int
     */
    public function findLocalReservationOverlap(LocalReservation $reservation): int
    {
        $qb = $this->createQueryBuilder('r');

        $c1 = $qb->expr()->andX(
            $qb->expr()->lte('r.date', ':begDate'),
            $qb->expr()->gte('r.endDate', ':begDate')
        );

        $c2 = $qb->expr()->andX(
            $qb->expr()->lte('r.date', ':endDate'),
            $qb->expr()->gte('r.endDate', ':endDate')
        );

        $c3 = $qb->expr()->andX(
            $qb->expr()->gte('r.date', ':begDate'),
            $qb->expr()->lte('r.endDate', ':endDate')
        );

        try {
            $qb->where($qb->expr()->orX($c1, $c2, $c3))
                ->setParameter('begDate', $reservation->getDate(), Types::DATETIME_MUTABLE)
                ->setParameter('endDate', $reservation->getEndDate(), Types::DATETIME_MUTABLE);
        } catch (Exception $e) {
        }

        $res = $qb->getQuery()->getResult();

        return count($res);

        //return [$reservation->getDate()->format("Y M d H:i"),
        //    $reservation->getDate()->add(new \DateInterval("PT" . $reservation->getDuration() . "M"))->format("Y M d H:i")];
    }
}
