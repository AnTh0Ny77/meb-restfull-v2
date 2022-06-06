<?php

namespace App\Entity;

use App\Repository\PoiScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoiScoreRepository::class)]
class PoiScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Poi::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $Poi;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'poiScores')]
    #[ORM\JoinColumn(nullable: false)]
    private $User;

    #[ORM\Column(type: 'integer')]
    private $score;

    #[ORM\Column(type: 'boolean')]
    private $finished;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoi(): ?Poi
    {
        return $this->Poi;
    }

    public function setPoi(?Poi $Poi): self
    {
        $this->Poi = $Poi;

        return $this;
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }
}
