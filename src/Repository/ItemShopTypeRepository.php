<?php

namespace App\Repository;

use App\Entity\ItemShopType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemShopType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemShopType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemShopType[]    findAll()
 * @method ItemShopType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemShopTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemShopType::class);
    }
}
