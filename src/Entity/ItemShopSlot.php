<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemShopSlotRepository")
 */
class ItemShopSlot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition", inversedBy="itemShopType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $edition;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemShopOrder", mappedBy="slot")
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ItemShopType", inversedBy="slots")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deliveryTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $orderTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $preOrderTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxOrder;

    public function getId()
    {
        return $this->id;
    }

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getType(): ?ItemShopType
    {
        return $this->type;
    }

    public function setType(?ItemShopType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOrders(): ?ItemShopOrder
    {
        return $this->orders;
    }

    public function setOrders(?ItemShopOrder $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getDeliveryTime(): ?\DateTime
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?\DateTime $deliveryTime): self
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    public function getOrderTime(): ?\DateTime
    {
        return $this->orderTime;
    }

    public function setOrderTime(?\DateTime $orderTime): self
    {
        $this->orderTime = $orderTime;

        return $this;
    }

    public function getPreOrderTime(): ?\DateTime
    {
        return $this->preOrderTime;
    }

    public function setPreOrderTime(?\DateTime $preOrderTime): self
    {
        $this->preOrderTime = $preOrderTime;

        return $this;
    }

    public function getMaxOrder(): ?int
    {
        return $this->maxOrder;
    }

    public function setMaxOrder(?int $maxOrder): self
    {
        $this->maxOrder = $maxOrder;

        return $this;
    }
}
