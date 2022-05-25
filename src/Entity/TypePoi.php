<?php

namespace App\Entity;

use App\Repository\TypePoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypePoiRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:TypePoi'],
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'summary' => 'public - retrieves a TypePoi collection  ',
                'security' => [['bearerAuth' => []]]
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:TypePoi'],
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'summary' => 'public - retrieves a single TypePoi   ',
                'security' => [['bearerAuth' => []]]
            ]
        ]
    ]
)]
class TypePoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Poi', 'read:Game' , 'read:TypePoi'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Poi' , 'read:Game' , 'read:TypePoi'])]
    private $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['read:Poi', 'read:Game' , 'read:TypePoi'])]
    private $color;

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
