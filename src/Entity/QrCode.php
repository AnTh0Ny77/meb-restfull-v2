<?php

namespace App\Entity;

use App\Repository\QrCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QrCodeRepository::class)]
class QrCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $idClient;

    #[ORM\Column(type: 'string', length: 255 , unique: true)]
    private $secret;

    #[ORM\Column(type: 'boolean')]
    private $qrLock;

    #[ORM\ManyToOne(targetEntity: Games::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $idGame;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $time;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClient(): ?user
    {
        return $this->idClient;
    }

    public function setIdClient(?user $idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getQrLock(): ?bool
    {
        return $this->qrLock;
    }

    public function setQrLock(bool $qrLock): self
    {
        $this->qrLock = $qrLock;

        return $this;
    }

    public function getIdGame(): ?Games
    {
        return $this->idGame;
    }

    public function setIdGame(?Games $idGame): self
    {
        $this->idGame = $idGame;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

   
}
