<?php

namespace App\Entity;

use App\Repository\ClientGamesRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientGamesRepository::class)]
class ClientGames
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Client:User'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'clientGames')]
    #[ORM\JoinColumn(nullable: false)]
    private $User;

    #[ORM\ManyToOne(targetEntity: Games::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Client:User'])]
    private $Game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getGame(): ?Games
    {
        return $this->Game;
    }

    public function setGame(?Games $Game): self
    {
        $this->Game = $Game;

        return $this;
    }
}
