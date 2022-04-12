<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SlideRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlideRepository::class)]
#[ApiResource]
class Slide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $Name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $Text;

    #[ORM\Column(type: 'text', nullable: true)]
    private $TextSuccess;

    #[ORM\Column(type: 'text', nullable: true)]
    private $TextFail;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Time;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Step;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Response;

    #[ORM\Column(type: 'boolean')]
    private $Penality;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $CoverPath;

    #[ORM\ManyToOne(targetEntity: Poi::class, inversedBy: 'slides')]
    #[ORM\JoinColumn(nullable: false)]
    private $Poi;

    #[ORM\ManyToOne(targetEntity: TypeSlide::class, inversedBy: 'Slide')]
    #[ORM\JoinColumn(nullable: false)]
    private $typeSlide;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(?string $Text): self
    {
        $this->Text = $Text;

        return $this;
    }

    public function getTextSuccess(): ?string
    {
        return $this->TextSuccess;
    }

    public function setTextSuccess(?string $TextSuccess): self
    {
        $this->TextSuccess = $TextSuccess;

        return $this;
    }

    public function getTextFail(): ?string
    {
        return $this->TextFail;
    }

    public function setTextFail(?string $TextFail): self
    {
        $this->TextFail = $TextFail;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->Time;
    }

    public function setTime(?int $Time): self
    {
        $this->Time = $Time;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->Step;
    }

    public function setStep(?int $Step): self
    {
        $this->Step = $Step;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->Response;
    }

    public function setResponse(?string $Response): self
    {
        $this->Response = $Response;

        return $this;
    }

    public function getPenality(): ?bool
    {
        return $this->Penality;
    }

    public function setPenality(bool $Penality): self
    {
        $this->Penality = $Penality;

        return $this;
    }

    public function getCoverPath(): ?string
    {
        return $this->CoverPath;
    }

    public function setCoverPath(?string $CoverPath): self
    {
        $this->CoverPath = $CoverPath;

        return $this;
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

    public function getTypeSlide(): ?TypeSlide
    {
        return $this->typeSlide;
    }

    public function setTypeSlide(?TypeSlide $typeSlide): self
    {
        $this->typeSlide = $typeSlide;

        return $this;
    }
}
