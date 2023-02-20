<?php

namespace App\Entity;

use App\Repository\BoardGameRepository;
use App\Entity\BoardGameReservation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BoardGameRepository::class)]
#[UniqueEntity("name")]
class BoardGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 190, unique: true)]
    #[Assert\Length(min: 1, max: 190)]
    private string $name;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\Positive()]
    private ?int $year;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\Positive()]
    private ?float $price;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $missing;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $note;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $excess;

    #[ORM\ManyToMany(targetEntity: BoardGameReservation::class, mappedBy: "boardGames")]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setID(int $id): self
    {
        $this->id = $id;

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

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMissing(): ?string
    {
        return $this->missing;
    }

    public function setMissing(?string $missing): self
    {
        $this->missing = $missing;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getExcess(): ?string
    {
        return $this->excess;
    }

    public function setExcess(?string $excess): self
    {
        $this->excess = $excess;

        return $this;
    }

    /**
     * @return Collection|BoardGameReservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(BoardGameReservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addBoardGame($this);
        }

        return $this;
    }

    public function removeReservation(BoardGameReservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            $reservation->removeBoardGame($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
