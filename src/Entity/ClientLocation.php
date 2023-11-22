<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
#[ORM\Table(name: "client_location")]
class ClientLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "clientLocations")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: "text", nullable: true)]
    private $textColumn;

    #[ORM\Column(type: "json", nullable: true)]
    private $jsonColumn;

    #[ORM\Column(type: "text", nullable: true)]
    private $postal;

    #[ORM\Column(type: "boolean", nullable: true)]
    private $booleanColumn;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTextColumn(): ?string
    {
        return $this->textColumn;
    }

    public function setTextColumn(?string $textColumn): self
    {
        $this->textColumn = $textColumn;

        return $this;
    }

    public function getJsonColumn(): ?array
    {
        return $this->jsonColumn;
    }

    public function setJsonColumn(?array $jsonColumn): self
    {
        $this->jsonColumn = $jsonColumn;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getBooleanColumn(): ?bool
    {
        return $this->booleanColumn;
    }

    public function setBooleanColumn(?bool $booleanColumn): self
    {
        $this->booleanColumn = $booleanColumn;

        return $this;
    }

    // You may also need to generate these methods using your IDE or Symfony console.
}
