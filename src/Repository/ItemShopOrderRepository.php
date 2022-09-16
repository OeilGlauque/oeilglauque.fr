<?php

namespace App\Repository;

use App\Entity\ItemShopOrder;
use App\Entity\Edition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemShopOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemShopOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemShopOrder[]    findAll()
 * @method ItemShopOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemShopOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemShopOrder::class);
    }
}
