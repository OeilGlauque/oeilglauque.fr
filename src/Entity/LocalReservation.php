<?php

namespace App\Entity;

use App\Repository\LocalReservationRepository;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\DateFormaterService as Formater;

#[ORM\Entity(repositoryClass: LocalReservationRepository::class)]
class LocalReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "localReservations")]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: "boolean")]
    private bool $validated;

    #[ORM\Column(type: "text")]
    private ?string $motif;


    #[ORM\Column(type: "datetime")]
    #[Assert\GreaterThanOrEqual("today")]
    #[Assert\LessThan("1 year")]
    #[Assert\NotBlank()]
    private \DateTime $date;


    #[ORM\Column(type: "datetime")]
    #[Assert\GreaterThanOrEqual("today")]
    #[Assert\LessThan("1 year")]
    #[Assert\NotBlank()]
    private \DateTime $endDate;

    public function __construct()
    {
        $this->validated = false;
        $this->date = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->endDate->diff($this->date)->i;
        /*try {
            return $this->getDate()->diff($this->endDate)->m;
        } catch(\Exception $e) {
            return 0;
        }*/
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(int $duration): self
    {
        $tmp = clone $this->date;
        $this->endDate = $tmp->add(new \DateInterval("PT".$duration."M"));

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

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFormatedDate(): string
    {
        return (new Formater())->format($this->date);
    }

    public function __toString()
    {
        return $this->author->getName() . ', ' . $this->getDate()->format("j M Y - H:i")
            . '-' . $this->getDuration();
    }
}