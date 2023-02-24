<?php

namespace App\Entity;

use App\Entity\Edition;
use App\Entity\ItemShopOrder;
use App\Entity\ItemShopType;
use App\Repository\ItemShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemShopRepository::class)]
class ItemShop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Edition::class, inversedBy: "items")]
    #[ORM\JoinColumn(nullable: false)]
    private $edition;

    #[ORM\OneToMany(targetEntity: ItemShopOrder::class, mappedBy: "item")]
    private Collection $orders;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank()]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: ItemShopType::class, inversedBy: "items")]
    private ItemShopType $type;

    #[ORM\Column(type: "float")]
    #[Assert\Positive()]
    #[Assert\NotNull()]
    private float $price;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
