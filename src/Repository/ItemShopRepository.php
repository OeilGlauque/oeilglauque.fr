<?php

namespace App\Repository;

use App\Entity\ItemShop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemShop|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemShop|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemShop[]    findAll()
 * @method ItemShop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemShop::class);
    }
}
