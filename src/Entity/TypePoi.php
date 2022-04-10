<?php

namespace App\Entity;

use App\Repository\TypePoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypePoiRepository::class)]
class TypePoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Poi'])]
    private $Name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['read:Poi'])]
    private $Color;

    #[ORM\OneToMany(mappedBy: 'typePoi', targetEntity: Poi::class)]
    private $Poi;

    public function __construct()
    {
        $this->Poi = new ArrayCollection();
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
     * @return Collection<int, Poi>
     */
    public function getPoi(): Collection
    {
        return $this->Poi;
    }

    public function addPoi(Poi $poi): self
    {
        if (!$this->Poi->contains($poi)) {
            $this->Poi[] = $poi;
            $poi->setTypePoi($this);
        }

        return $this;
    }

    public function removePoi(Poi $poi): self
    {
        if ($this->Poi->removeElement($poi)) {
            // set the owning side to null (unless already changed)
            if ($poi->getTypePoi() === $this) {
                $poi->setTypePoi(null);
            }
        }

        return $this;
    }
}
