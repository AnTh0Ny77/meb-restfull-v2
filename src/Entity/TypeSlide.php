<?php

namespace App\Entity;

use App\Repository\TypeSlideRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeSlideRepository::class)]
class TypeSlide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Slide' , 'read:Game' ,'read:Poi'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Slide' , 'read:Game' ,'read:Poi'])]
    private $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['read:Slide' , 'read:Game' ,'read:Poi'])]
    private $color;

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
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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
