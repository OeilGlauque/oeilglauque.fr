<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocalReservationRepository")
 */
class LocalReservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="partiesOrganisees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forceOnlineSeats;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameSlot", inversedBy="games")
     * @ORM\JoinColumn(nullable=true)
     */
    private $gameSlot;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\Column(type="text")
     */
    private $note;


    public function __construct()
    {
        $this->validated = false;
    }

    public function getId()
    {
        return $this->id;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getGameSlot(): ?GameSlot
    {
        return $this->gameSlot;
    }

    public function setGameSlot(?GameSlot $gameSlot): self
    {
        $this->gameSlot = $gameSlot;

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
}
