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
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ItemShopSlot", inversedBy="orders")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $slot;

    /**
     * @ORM\Column(type="text")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\Column(type="boolean")
     */
    private $collected;

    public function __construct()
    {
        $this->collected = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getItem(): ?ItemShop
    {
        return $this->item;
    }

    public function setItem(?ItemShop $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getSlot(): ?ItemShopSlot
    {
        return $this->slot;
    }

    public function setSlot(?ItemShopSlot $slot): self
    {
        $this->slot = $slot;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getTime(): ?\Datetime
    {
        return $this->time;
    }

    public function setTime(?\Datetime $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCollected(): ?bool
    {
        return $this->collected;
    }

    public function setCollected(?bool $collected): self
    {
        $this->collected = $collected;

        return $this;
    }
}
