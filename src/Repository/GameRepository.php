<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Edition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return Game[]
     */
    public function getOrderedGameList(Edition $edition, $validated): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.gameSlot', 'gs')
            ->leftJoin('gs.edition', 'ed')
            ->where('ed = :edition')
            ->andWhere('g.validated = :validated')
            ->setParameter('edition', $edition)
            ->setParameter('validated', $validated)
            ->orderBy('g.gameSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function search(String $query, $validated, Edition $edition): array{
        return $this->createQueryBuilder('g')
            ->leftJoin('g.gameSlot', 'gs')
            ->leftJoin('gs.edition', 'ed')
            ->where('g.validated = :validated')
            ->andWhere('ed = :edition')
            ->andWhere('g.title LIKE :query')
            ->setParameter('validated', $validated)
            ->setParameter('edition', $edition)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('g.gameSlot', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Game[] Returns an array of Game objects
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
    public function findOneBySomeField($value): ?Game
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
