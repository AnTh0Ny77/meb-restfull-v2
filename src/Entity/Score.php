<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]

class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'scores')]
    #[ORM\JoinColumn(nullable: false)]
    private $User;

    #[ORM\ManyToOne(targetEntity: Slide::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $Slide;

    #[ORM\Column(type: 'smallint')]
    private $Point;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Value;

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

    public function getSlide(): ?Slide
    {
        return $this->Slide;
    }

    public function setSlide(?Slide $Slide): self
    {
        $this->Slide = $Slide;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->Point;
    }

    public function setPoint(int $Point): self
    {
        $this->Point = $Point;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->Value;
    }

    public function setValue(?string $Value): self
    {
        $this->Value = $Value;

        return $this;
    }
}
