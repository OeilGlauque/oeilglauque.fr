<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopBoardGameRepository")
 */
class ShopBoardGameQuantity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BoardGameOrder", inversedBy="boardGamesQuantity")
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopBoardGame", inversedBy="boardGamesQuantity")
     */
    private $boardGames;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setID(int $id): self
    {
        $this->id = $id;
        
        return $this;
    }
    
    public function getQuantity(): ?string
    {
        return $this->quantity;
    }
    
    public function setQuantity(int $quant): self
    {
        $this->quantity = $quant;
        
        return $this;
    }

    /**
     * @return Collection|BoardGameOrder[]
     */
    public function getBoardGamesOrder(): Collection
    {
        return $this->orders;
    }

    public function addBoardGameOrder(BoardGameOrder $boardGameOrder): self
    {
        if (!$this->orders->contains($boardGameOrder)) {
            $this->orders[] = $boardGameOrder;
        }

        return $this;
    }

    public function removeBoardGameOrder(BoardGameOrder $boardGameOrder): self
    {
        if ($this->orders->contains($boardGameOrder)) {
            $this->orders->removeElement($boardGameOrder);
        }

        return $this;
    }

    /**
     * @return Collection|ShopBoardGame[]
     */
    public function getBoardGames(): Collection
    {
        return $this->boardGames;
    }

    public function addBoardGame(ShopBoardGame $boardGame): self
    {
        if (!$this->boardGames->contains($boardGame)) {
            $this->boardGames[] = $boardGame;
        }

        return $this;
    }

    public function removeBoardGame(ShopBoardGame $boardGame): self
    {
        if ($this->boardGames->contains($boardGame)) {
            $this->boardGames->removeElement($boardGame);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getId();
    }
}
