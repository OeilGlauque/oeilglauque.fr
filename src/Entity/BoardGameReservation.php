<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoardGameReservationRepository")
 */
class BoardGameReservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="boardGameReservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\Column(type="text")
     */
    private $note;

    /**
     * @ORM\Column(type="date")
     */
    private $dateBeg;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEnd;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\BoardGame", inversedBy="reservations")
     */
    private $boardGames;

    public function __construct()
    {
        $this->validated = false;
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

    public function getDateBeg(): ?\DateTimeInterface
    {
        return $this->dateBeg;
    }

    public function setDateBeg(\DateTimeInterface $date): self
    {
        $this->dateBeg = $date;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $date): self
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
    
}
