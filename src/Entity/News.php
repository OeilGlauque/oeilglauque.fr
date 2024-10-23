<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 255)]
    private string $title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank()]
    private string $text;

    #[ORM\Column(type: "string", length: 128)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 128)]
    private string $slug;

    #[ORM\Column(type: "datetime_immutable")]
    #[Assert\NotNull()]
    private DateTimeImmutable $date_created;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "newsRedacted")]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    public function __construct()
    {
        $this->date_created = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

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
}
