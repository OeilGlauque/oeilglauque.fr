<?php

namespace App\Entity;

use App\Repository\PosterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PosterRepository::class)]
class Poster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'poster')]
    private ?Edition $edition = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $fogImg = null;

    #[ORM\Column(length: 255)]
    private ?string $authors = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $concertImg = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition): static
    {
        $this->edition = $edition;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getFogImg(): ?string
    {
        return $this->fogImg;
    }

    public function setFogImg(string $fogImg): static
    {
        $this->fogImg = $fogImg;

        return $this;
    }

    public function getAuthors(): ?string
    {
        return $this->authors;
    }

    public function setAuthors(string $authors): static
    {
        $this->authors = $authors;

        return $this;
    }

    public function getConcertImg(): ?string
    {
        return $this->concertImg;
    }

    public function setConcertImg(?string $concertImg): static
    {
        $this->concertImg = $concertImg;

        return $this;
    }
}
