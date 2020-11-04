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
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopBoardGame", inversedBy="boardGamesQuantity")
     */
    private $boardGame;

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
    
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        
        return $this;
    }

    /**
     * @return BoardGameOrder
     */
    public function getBoardGamesOrder(): BoardGameOrder
    {
        return $this->order;
    }

    public function setBoardGameOrder(BoardGameOrder $boardGameOrder): self
    {
        $this->order = $boardGameOrder;

        return $this;
    }

    /**
     * @return ShopBoardGame
     */
    public function getBoardGame(): ShopBoardGame
    {
        return $this->boardGame;
    }

    public function setBoardGame(ShopBoardGame $boardGame): self
    {
        $this->boardGame = $boardGame;

        return $this;
    }

    public function __toString()
    {
        return $this->getBoardGame()->getName() . " x" . $this->getQuantity();
    }
}
