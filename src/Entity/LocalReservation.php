<?php

namespace App\Entity;

use DateTime;
use FG\ASN1\Universal\Integer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocalReservationRepository")
 */
class LocalReservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="localReservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\Column(type="text")
     */
    private $motif;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("today")
     * @Assert\LessThan("1 year")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("today")
     * @Assert\LessThan("1 year")
     */
    private $endDate;

    public function __construct()
    {
        $this->validated = false;
        $this->date = new DateTime();
    }

    public function getId()
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

    public function getAuthor(): ?User
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
        try {
            return $this->getDate()->diff($this->endDate)->m;
        } catch(\Exception $e) {
            return 0;
        }
    }

    public function setDuration(float $duration): self
    {
        $this->endDate = \DateTimeImmutable::createFromMutable($this->getDate())->add(new \DateInterval("PT".$duration."M"));

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

    public function __toString()
    {
        return $this->author->getName() . ', ' . $this->getDate()->format("j M Y - H:i")
            . '-' . $this->getDuration();
    }
}
