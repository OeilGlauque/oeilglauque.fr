<?php

namespace App\Repository;

use App\Entity\GoogleAuthToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoogleAuthToken>
 *
 * @method GoogleAuthToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleAuthToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleAuthToken[]    findAll()
 * @method GoogleAuthToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleAuthTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleAuthToken::class);
    }

    /**
     * Return the last saved token.
     *
     * @return GoogleAuthToken
     */
    public function findLastToken(): GoogleAuthToken
    {
        $qb = $this->createQueryBuilder("t")
                    ->orderBy('t.id','DESC')
                    ->setMaxResults(1);

        return $qb->getQuery()->getResult()[0];
    }

//    /**
//     * @return GoogleAuthToken[] Returns an array of GoogleAuthToken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GoogleAuthToken
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
