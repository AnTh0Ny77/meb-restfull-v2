<?php

namespace App\Entity;

use App\Repository\TypeSlideRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeSlideRepository::class)]
class TypeSlide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $Name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $Color;

    #[ORM\OneToMany(mappedBy: 'typeSlide', targetEntity: Slide::class)]
    private $Slide;

    public function __construct()
    {
        $this->Slide = new ArrayCollection();
    }

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

    public function getColor(): ?string
    {
        return $this->Color;
    }

    public function setColor(?string $Color): self
    {
        $this->Color = $Color;

        return $this;
    }

    /**
     * @return Collection<int, Slide>
     */
    public function getSlide(): Collection
    {
        return $this->Slide;
    }

    public function addSlide(Slide $slide): self
    {
        if (!$this->Slide->contains($slide)) {
            $this->Slide[] = $slide;
            $slide->setTypeSlide($this);
        }

        return $this;
    }

    public function removeSlide(Slide $slide): self
    {
        if ($this->Slide->removeElement($slide)) {
            // set the owning side to null (unless already changed)
            if ($slide->getTypeSlide() === $this) {
                $slide->setTypeSlide(null);
            }
        }

        return $this;
    }
}
