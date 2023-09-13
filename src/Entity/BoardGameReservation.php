<?php

namespace App\Entity;

use App\Repository\BoardGameReservationRepository;
use App\Entity\BoardGame;
use App\Entity\User;
use App\Service\DateFormaterService as Formater;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BoardGameReservationRepository::class)]
class BoardGameReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "boardGameReservations")]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $validated;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $note;

    #[ORM\Column(type: "datetime")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "Le début de la réservation ne peut pas être avant aujourd'hui.")]
    #[Assert\LessThan(
        value: "1 year",
        message: "Le début de la réservation doit être dans moins d'un an.")]
    #[Assert\NotBlank()]
    private \DateTime $dateBeg;


    #[ORM\Column(type: "datetime")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La fin de la réservation ne peut pas être avant aujourd'hui.")]
    #[Assert\LessThan(
        value: "1 year",
        message: "La fin de la réservation doit être dans moins d'un an.")]
    #[Assert\NotBlank()]
    private \DateTime $dateEnd;

    #[ORM\ManyToMany(targetEntity: BoardGame::class, inversedBy: "reservations")]
    private Collection $boardGames;

    public function __construct()
    {
        $this->validated = null;
        $this->boardGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDateBeg(): ?\DateTime
    {
        return $this->dateBeg;
    }

    public function getFormatedDateBeg(): ?string
    {
        return (new Formater("EEEE d MMMM Y"))->format($this->dateBeg);
    }

    public function setDateBeg(\DateTime $date): self
    {
        $this->dateBeg = $date;

        return $this;
    }

    public function getDateEnd(): ?\DateTime
    {
        return $this->dateEnd;
    }

    public function getFormatedDateEnd(): ?string
    {
        return (new Formater("EEEE d MMMM Y"))->format($this->dateEnd);
    }

    public function setDateEnd(\DateTime $date): self
    {
        $this->dateEnd = $date;

        return $this;
    }

    /**
     * @return Collection|BoardGame[]
     */
    public function getBoardGames(): Collection
    {
        return $this->boardGames;
    }

    public function addBoardGame(BoardGame $boardGame): self
    {
        if (!$this->boardGames->contains($boardGame)) {
            $this->boardGames[] = $boardGame;
        }

        return $this;
    }

    public function removeBoardGame(BoardGame $boardGame): self
    {
        if ($this->boardGames->contains($boardGame)) {
            $this->boardGames->removeElement($boardGame);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->author->getName() . ', ' . $this->getDateBeg()->format("j M Y")
            . '-' . $this->getDateEnd()->format("j M Y")
            . ' (' . implode(", ", $this->boardGames->toArray()) . ')';
    }
}
