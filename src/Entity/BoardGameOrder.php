<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoardGameOrderRepository")
 */
class BoardGameOrder
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShopBoardGameQuantity", mappedBy="orders")
     */
    private $boardGamesQuantity;

    public function __construct()
    {
        $this->boardGamesQuantity = new ArrayCollection();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection|ShopBoardGameQuantity[]
     */
    public function getBoardGamesQuantity(): Collection
    {
        return $this->boardGamesQuantity;
    }

    public function addBoardGameQuantity(ShopBoardGameQuantity $boardGamesQuantity): self
    {
        if ($this->boardGamesQuantity->contains($boardGamesQuantity)) {
            $this->boardGamesQuantity[] = $boardGamesQuantity;
        }

        return $this;
    }

    public function removeBoardGameQuantity(ShopBoardGameQuantity $boardGamesQuantity): self
    {
        if ($this->boardGamesQuantity->contains($boardGamesQuantity)) {
            $this->boardGamesQuantity->removeElement($boardGamesQuantity);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName() . $this->getSurname() 
        . ' (' . implode(", ", $this->boardGamesQuantity->toArray()) . ')';
    }
}
