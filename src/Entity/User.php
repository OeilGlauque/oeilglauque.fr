<?php

Namespace App\Entity;

use App\Entity\BoardGameReservation;
use App\Entity\Game;
use App\Entity\LocalReservation;
use App\Entity\News;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Table(name: "app_users")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity("pseudo")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "string", length: 64)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 64)]
    private string $name;

    #[ORM\Column(type: "string", length: 64)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 64)]
    private string $firstName;

    #[ORM\Column(type: "string", length: 64, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 64)]
    private string $pseudo;

    #[ORM\Column(type: "string", length: 64)]
    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 5,
        max: 64,
        minMessage: "Votre mot de passe doit contenir au moins {{ limit }} caractères",
        maxMessage: "Votre mot de passe ne peut pas contenir plus de {{ limit }} caractères")]
    private $password;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $resetToken;

    #[ORM\Column(type: "string", length: 254)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private string $email;

    #[ORM\Column(type: "string", length: 512, nullable: true)]
    #[Assert\Length(max: 512)]
    private ?string $avatar;

    #[ORM\Column(type: "datetime_immutable")]
    #[Assert\NotNull()]
    private DateTimeImmutable $dateCreated;

    #[ORM\OneToMany(targetEntity: News::class, mappedBy: "author")]
    private Collection $newsRedacted;

    #[ORM\Column(name: "is_active", type: "boolean")]
    private bool $isActive;

    #[ORM\Column(type: "text")]
    private ?string $roles;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: "author")]
    private Collection $partiesOrganisees;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: "players")]
    private Collection $partiesJouees;

    #[ORM\OneToMany(targetEntity: LocalReservation::class, mappedBy: "author")]
    private Collection $localReservations;

    #[ORM\OneToMany(targetEntity: BoardGameReservation::class, mappedBy: "author")]
    private Collection $boardGameReservations;

    public function __construct()
    {
        $this->newsRedacted = new ArrayCollection();
        $this->roles = "ROLE_USER";
        $this->dateCreated = new \DateTimeImmutable();
        $this->isActive = true;
        $this->resetToken = null;
        $this->partiesOrganisees = new ArrayCollection();
        $this->partiesJouees = new ArrayCollection();
        $this->boardGameReservations = new ArrayCollection();
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

    /*public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }*/

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken)
    {
        $this->resetToken = $resetToken;
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
    /*public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->pseudo,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }*/

    /** @see \Serializable::unserialize() */
    /*public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->pseudo,
            $this->password
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }*/

    // To assure implementation of UserInterface
    public function getUsername(): string
    {
        return $this->getPseudo();
    }

    public function getUserIdentifier(): string
    {
        return $this->getPseudo();
    }

    public function getRoles(): array
    {
        $roles = preg_split("/;/",$this->roles);

        return array_unique($roles);
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

    public function hasRole(string $role) {
        return in_array($role, $this->getRoles());
    }

    /**
     * @return Collection|Game[]
     */
    public function getPartiesOrganisees(): Collection
    {
        return $this->partiesOrganisees;
    }

    public function addPartiesOrganisee(Game $partiesOrganisee): self
    {
        if (!$this->partiesOrganisees->contains($partiesOrganisee)) {
            $this->partiesOrganisees[] = $partiesOrganisee;
            $partiesOrganisee->setAuthor($this);
        }

        return $this;
    }

    public function removePartiesOrganisee(Game $partiesOrganisee): self
    {
        if ($this->partiesOrganisees->contains($partiesOrganisee)) {
            $this->partiesOrganisees->removeElement($partiesOrganisee);
            // set the owning side to null (unless already changed)
            if ($partiesOrganisee->getAuthor() === $this) {
                $partiesOrganisee->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getPartiesJouees(): Collection
    {
        return $this->partiesJouees;
    }

    public function addPartiesJouee(Game $partiesJouee): self
    {
        if (!$this->partiesJouees->contains($partiesJouee)) {
            $this->partiesJouees[] = $partiesJouee;
            $partiesJouee->addPlayer($this);
        }

        return $this;
    }

    public function removePartiesJouee(Game $partiesJouee): self
    {
        if ($this->partiesJouees->contains($partiesJouee)) {
            $this->partiesJouees->removeElement($partiesJouee);
            $partiesJouee->removePlayer($this);
        }

        return $this;
    }

    /**
     * @return Collection|LocalReservation[]
     */
    public function getLocalReservation(): Collection
    {
        return $this->localReservations;
    }

    public function addLocalReservation(LocalReservation $localReservation): self
    {
        if (!$this->localReservations->contains($localReservation)) {
            $this->localReservations[] = $localReservation;
            $localReservation->setAuthor($this);
        }

        return $this;
    }

    public function removeLocalReservation(LocalReservation $localReservation): self
    {
        if ($this->localReservations->contains($localReservation)) {
            $this->localReservations->removeElement($localReservation);
            if ($localReservation->getAuthor() === $this) {
                $localReservation->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BoardGameReservation[]
     */
    public function getBoardGameReservations(): Collection
    {
        return $this->boardGameReservations;
    }

    public function addBoardGameReservation(BoardGameReservation $boardGameReservation): self
    {
        if (!$this->boardGameReservations->contains($boardGameReservation)) {
            $this->boardGameReservations[] = $boardGameReservation;
            $boardGameReservation->setAuthor($this);
        }

        return $this;
    }

    public function removeBoardGameReservation(BoardGameReservation $boardGameReservation): self
    {
        if ($this->boardGameReservations->contains($boardGameReservation)) {
            $this->boardGameReservations->removeElement($boardGameReservation);
            // set the owning side to null (unless already changed)
            if ($boardGameReservation->getAuthor() === $this) {
                $boardGameReservation->setAuthor(null);
            }
        }

        return $this;
    }
}
