<?php

namespace App\Entity;

use App\Entity\Edition;
use App\Entity\ItemShopOrder;
use App\Entity\ItemShopType;
use App\Repository\ItemShopSlotRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemShopSlotRepository::class)]
class ItemShopSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Edition::class, inversedBy: "itemShopType")]
    #[ORM\JoinColumn(nullable: false)]
    private Edition $edition;

    #[ORM\OneToMany(targetEntity: ItemShopOrder::class, mappedBy: "slot")]
    private Collection $orders;

    #[ORM\ManyToOne(targetEntity: ItemShopType::class, inversedBy: "slots")]
    private ItemShopType $type;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull()]
    private DateTime $deliveryTime;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull()]
    private DateTime $orderTime;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $preOrderTime;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $maxOrder;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

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

    public function getOrders(): ?Collection
    {
        return $this->orders;
    }

    public function setOrders(?Collection $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getDeliveryTime(): ?DateTime
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?DateTime $deliveryTime): self
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    public function getOrderTime(): ?DateTime
    {
        return $this->orderTime;
    }

    public function setOrderTime(?DateTime $orderTime): self
    {
        $this->orderTime = $orderTime;

        return $this;
    }

    public function getPreOrderTime(): ?DateTime
    {
        return $this->preOrderTime;
    }

    public function setPreOrderTime(?DateTime $preOrderTime): self
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
