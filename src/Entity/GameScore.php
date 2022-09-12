<?php

namespace App\Entity;

use App\Repository\GameScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameScoreRepository::class)]
class GameScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Game')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Games::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $game;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $score;

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

    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $Game): self
    {
        $this->game = $Game;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }
}
