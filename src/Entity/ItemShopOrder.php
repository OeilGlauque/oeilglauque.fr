<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemShopOrderRepository")
 */
class ItemShopOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ItemShop", inversedBy="orders")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ItemShopSlot", inversedBy="orders")
     */
    private $slot;

    /**
     * @ORM\Column(type="text")
     */
    private $pseudo;

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
