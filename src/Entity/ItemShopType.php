<?php

namespace App\Entity;

use App\Entity\ItemShop;
use App\Entity\ItemShopSlot;
use App\Repository\ItemShopTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemShopTypeRepository::class)]
class ItemShopType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "text")]
    private string $type;

    #[ORM\OneToMany(targetEntity: ItemShop::class, mappedBy: "type")]
    private Collection $items;

    #[ORM\OneToMany(targetEntity: ItemShopSlot::class, mappedBy: "type")]
    private Collection $slots;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->slots = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?String
    {
        return $this->type;
    }

    public function setType(?String $type): self
    {
        $this->type = $type;

        return $this;
    }
}
