<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GamesRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'openapi_context' => [
                'summary' => 'public query that retrieves a games collection  ',
            ],
            'normalization_context' => ['groups' => ['read:Games']]
        ]
    ],
    itemOperations: [ 
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'openapi_context' => [
                'summary' => 'public query that retrieves a single game',
            ],
            'normalization_context' => ['groups' => ['read:Games']]
        ]
    ],
    normalizationContext: ['groups' => ['read:Games'], "enable_max_depth" => true]
)]
class Games
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([ 'read:Games'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Games'])]
    private $Name;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['read:Games'])]
    private $Destination;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Games'])]
    private $CoverPath;

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

    public function getDestination(): ?string
    {
        return $this->Destination;
    }

    public function setDestination(?string $Destination): self
    {
        $this->Destination = $Destination;

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
}
