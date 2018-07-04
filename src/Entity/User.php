<?php

Namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;
    
    /**
    * @ORM\Column(type="string", length=254, unique=true)
    */
    private $email;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $dateCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News", mappedBy="Author")
     */
    private $newsRedacted;

    /**
     * @ORM\Column(name="is_active", type="boolean", options={"default": true})
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;


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
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->pseudo,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->pseudo,
            $this->password
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    // To assure implementation of UserInterface
    public function getUsername(): ?string
    {
        return $this->getPseudo();
    }
    public function getRoles()
    {
        return explode(";", $this->roles);
    }
    public function eraseCredentials()
    {
    }
    public function getSalt()
    {
        return null;
    }

    public function setRoles(?string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
