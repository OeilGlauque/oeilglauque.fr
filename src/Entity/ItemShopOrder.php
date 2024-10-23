<?php

namespace App\Entity;

use App\Entity\ItemShop;
use App\Entity\ItemShopSlot;
use App\Repository\ItemShopOrderRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemShopOrderRepository::class)]
class ItemShopOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ItemShop::class, inversedBy: "orders")]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ItemShop $item;

    #[ORM\ManyToOne(targetEntity: ItemShopSlot::class, inversedBy: "orders")]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ItemShopSlot $slot;

    #[ORM\Column(type: "text")]
    private string $pseudo;

    #[ORM\Column(type: "datetime")]
    private DateTime $time;

    #[ORM\Column(type: "boolean")]
    private bool $collected;

    public function __construct()
    {
        $this->collected = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getItem(): ?ItemShop
    {
        return $this->item;
    }

    public function setItem(?ItemShop $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getSlot(): ?ItemShopSlot
    {
        return $this->slot;
    }

    public function setSlot(?ItemShopSlot $slot): self
    {
        $this->slot = $slot;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getTime(): ?Datetime
    {
        return $this->time;
    }

    public function setTime(?Datetime $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCollected(): ?bool
    {
        return $this->collected;
    }

    public function setCollected(?bool $collected): self
    {
        $this->collected = $collected;

        return $this;
    }
}
