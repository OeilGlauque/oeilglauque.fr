<?php

Namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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
    private $Name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $FirstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Pseudo;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $Avatar;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $dateCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News", mappedBy="Author")
     */
    private $newsRedacted;

    public function __construct()
    {
        $this->newsRedacted = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $Pseudo): self
    {
        $this->Pseudo = $Pseudo;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->Avatar;
    }

    public function setAvatar(?string $Avatar): self
    {
        $this->Avatar = $Avatar;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return Collection|News[]
     */
    public function getNewsRedacted(): Collection
    {
        return $this->newsRedacted;
    }

    public function addNewsRedacted(News $newsRedacted): self
    {
        if (!$this->newsRedacted->contains($newsRedacted)) {
            $this->newsRedacted[] = $newsRedacted;
            $newsRedacted->setAuthor($this);
        }

        return $this;
    }

    public function removeNewsRedacted(News $newsRedacted): self
    {
        if ($this->newsRedacted->contains($newsRedacted)) {
            $this->newsRedacted->removeElement($newsRedacted);
            // set the owning side to null (unless already changed)
            if ($newsRedacted->getAuthor() === $this) {
                $newsRedacted->setAuthor(null);
            }
        }

        return $this;
    }
}
