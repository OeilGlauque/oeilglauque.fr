<?php

namespace App\Entity;

use App\Repository\GameRepository;
use App\Entity\User;
use App\Entity\GameSlot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NotBlank()]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "string", length: 1024, nullable: true)]
    #[Assert\Length(max: 1024)]
    private ?string $image;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "partiesOrganisees")]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: "integer")]
    #[Assert\Range(
        min: 1,
        max: 15,
        minMessage: "Il faut au moins un joueur pour s'amuser...",
        maxMessage: "Notre capacité d'accueil est limitée !" )]
    private int $seats;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "partiesJouees")]
    private Collection $players;

    #[ORM\Column(type: "string", length: 1024, nullable: true)]
    #[Assert\Length(max: 1024)]
    private ?string $tags;

    #[ORM\Column(type: "boolean")]
    private bool $forceOnlineSeats;

    #[ORM\ManyToOne(targetEntity: GameSlot::class, inversedBy: "games")]
    #[ORM\JoinColumn(nullable: true)]
    private GameSlot $gameSlot;

    #[ORM\Column(type: "boolean")]
    private bool $validated;

    #[ORM\Column(type: "boolean")]
    private bool $locked;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->validated = false;
        $this->locked = false;
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

    public function getOfflineSeats() :?int
    {
        return ($this->getForceOnlineSeats() ? 0 : ceil($this->getSeats()/2));
    }

    public function getBookedSeats() :?int
    {
        return (count($this->getPlayers()));
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


    public function getTags(): ?string
    {
        return implode(';', preg_split('/( *[,;] *)/', $this->tags));
    }

    public function hasTag(string $tag): ?bool
    {
        return in_array($tag, preg_split('/( *[,;] *)/', $this->tags));
    }

    public function listTags(): ?string
    {
        return implode(', ', preg_split('/( *[,;] *)/', $this->tags));
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

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

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }
}
