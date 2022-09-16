<?php

namespace App\Repository;

use App\Entity\ItemShopSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemShopSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemShopSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemShopSlot[]    findAll()
 * @method ItemShopSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemShopSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemShopSlot::class);
    }
}
