<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemShopRepository")
 */
class ItemShop
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $edition;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemShopOrder", mappedBy="slot")
     */
    private $orders;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ItemShopType", inversedBy="items")
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
