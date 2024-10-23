<?php

namespace App\Entity;

use App\Repository\GoogleAuthTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GoogleAuthTokenRepository::class)]
class GoogleAuthToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $access_token = null;

    #[ORM\Column(length: 255)]
    private ?string $refresh_token = null;

    #[ORM\Column]
    private ?int $created = null;

    #[ORM\Column]
    private ?int $expires_in = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(string $access_token): static
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(string $refresh_token): static
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(int $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expires_in;
    }

    public function setExpiresIn(int $expires_in): static
    {
        $this->expires_in = $expires_in;

        return $this;
    }

    public function getToken(): array
    {
        return [
            "access_token" => $this->access_token,
            "refresh_token" => $this->refresh_token,
            "created" => $this->created,
            "expires_in" => $this->expires_in
        ];
    }
}
