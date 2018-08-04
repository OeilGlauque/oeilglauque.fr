<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="partiesOrganisees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 100,
     *      minMessage = "Il faut au moins un joueur pour s'amuser...",
     *      maxMessage = "Notre capacité d'accueil est limitée !" )
     */
    private $seats;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="partiesJouees")
     */
    private $players;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forceOnlineSeats;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameSlot", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameSlot;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): self
    {
        $this->seats = $seats;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(User $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
        }

        return $this;
    }

    public function removePlayer(User $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
        }

        return $this;
    }

    public function getFreeSeats() :?int 
    {
        return ($this->getForceOnlineSeats() ? $this->getSeats() - count($this->getPlayers()) : floor($this->getSeats()/2) - count($this->getPlayers()));
    }

    public function getForceOnlineSeats(): ?bool
    {
        return $this->forceOnlineSeats;
    }

    public function setForceOnlineSeats(bool $forceOnlineSeats): self
    {
        $this->forceOnlineSeats = $forceOnlineSeats;

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
}
