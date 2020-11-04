<?php

namespace App\Repository;

use App\Entity\BoardGameOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoardGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardGame[]    findAll()
 * @method BoardGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardGameOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardGameOrder::class);
    }

    public function getFullTotal()
    {
        $orders = $this->findAll();
        $fullTotal = 0.0;
        foreach($orders as $order) {
            $fullTotal += $order->getTotal();
        }
        return $fullTotal;
    }
}
