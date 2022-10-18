<?php

namespace App\Entity;

use App\Repository\TransacRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransacRepository::class)]
class Transac
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Client:User'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'transacs')]
    private $user;

    #[ORM\Column(type: 'float')]
    #[Groups(['read:Client:User'])]
    private $amount;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Client:User'])]
    private $createdAt;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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
