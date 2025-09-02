<?php

namespace App\Entity;

use App\Repository\EditionRepository;
use App\Entity\GameSlot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EditionRepository::class)]
class Edition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $annee;

    #[ORM\Column(type: "text")]
    private $homeText;

    #[ORM\OneToMany(targetEntity: GameSlot::class, mappedBy: "edition")]
    private Collection $gameSlots;

    #[ORM\OneToMany(targetEntity: ItemShop::class, mappedBy: "edition")]
    private Collection $items;

    #[ORM\OneToMany(targetEntity: ItemShopSlot::class, mappedBy: "edition")]
    private Collection $itemShopType;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Length(min: 0, max: 255)]
    private $dates = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private string $type = 'FOG';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end = null;

    public function __construct()
    {
        $this->gameSlots = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->itemShopType = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getHomeText(): ?string
    {
        return $this->homeText;
    }

    public function setHomeText(string $homeText): self
    {
        $this->homeText = $homeText;

        return $this;
    }

    /**
     * @return Collection|GameSlot[]
     */
    public function getGameSlots(): Collection
    {
        return $this->gameSlots;
    }

    public function addGameSlot(GameSlot $gameSlot): self
    {
        if (!$this->gameSlots->contains($gameSlot)) {
            $this->gameSlots[] = $gameSlot;
            $gameSlot->setEdition($this);
        }

        return $this;
    }

    public function removeGameSlot(GameSlot $gameSlot): self
    {
        if ($this->gameSlots->contains($gameSlot)) {
            $this->gameSlots->removeElement($gameSlot);
            // set the owning side to null (unless already changed)
            if ($gameSlot->getEdition() === $this) {
                $gameSlot->setEdition(null);
            }
        }

        return $this;
    }

    public function getDates(): ?string
    {
        if ($this->start !== null) {
            return "Du " . $this->getStart()->format('d') . " au " . $this->getEnd()->format('d') . " " . $this->getEnd()->format('M');
        }
        return $this->dates;
    }

    public function setDates(string $dates): self
    {
        $this->dates = $dates;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }
}
