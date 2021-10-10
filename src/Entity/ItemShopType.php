<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemShopTypeRepository")
 */
class ItemShopType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemShop", mappedBy="type")
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemShopSlot", mappedBy="type")
     */
    private $slots;

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
