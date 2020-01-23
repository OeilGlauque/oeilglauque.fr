<?php

namespace App\Repository;

use App\Entity\LocalReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LocalReservationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
            ->setParameter('yesterday', new \DateTime('-1 day'), \Doctrine\DBAL\Types\Type::DATETIME)
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
            ->setParameter('today', new \DateTime(), \Doctrine\DBAL\Types\Type::DATETIME)
            ->addOrderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}