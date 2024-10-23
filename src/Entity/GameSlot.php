<?php

namespace App\Entity;

use App\Entity\Edition;
use App\Entity\Game;
use App\Repository\GameSlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameSlotRepository::class)]
class GameSlot
{
     #[ORM\Id]
     #[ORM\GeneratedValue]
     #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Edition::class, inversedBy: "gameSlots")]
    #[ORM\JoinColumn(nullable: false)]
    private Edition $edition;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: "gameSlot")]
    private Collection $games;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 255)]
    private string $text;

   #[ORM\Column(type: "integer")]
   #[Assert\PositiveOrZero()]
    private $maxGames;

    public function __construct()
    {
        $this->games = new ArrayCollection();
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

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setGameSlot($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getGameSlot() === $this) {
                $game->setGameSlot(null);
            }
        }

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getMaxGames(): ?int
    {
        return $this->maxGames;
    }

    public function setMaxGames(int $maxGames): self
    {
        $this->maxGames = $maxGames;

        return $this;
    }
}
