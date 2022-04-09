<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PoiRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoiRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Poi'],
            'openapi_context' => [
                'summary' => 'public - retrieves a Poi collection  ',
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Poi'],
            'openapi_context' => [
                'summary' => 'public - retrieves a single Poi ',
            ]
        ]
    ]
)]
class Poi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Quest', 'read:oneQuest' , 'read:Poi' ])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi'])]
    private $Name;

    #[ORM\Column(type: 'json')]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi'])]
    private $Latlng = [];

    #[ORM\ManyToOne(targetEntity: Quest::class, inversedBy: 'poi')]
    #[Groups(['read:Poi'])]
    private $Quest;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Poi'])]
    private $Text;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Poi'])]
    private $clue;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['read:Poi'])]
    private $ImageClue;

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

    public function getLatlng(): ?array
    {
        return $this->Latlng;
    }

    public function setLatlng(array $Latlng): self
    {
        $this->Latlng = $Latlng;

        return $this;
    }

    public function getQuest(): ?Quest
    {
        return $this->Quest;
    }

    public function setQuest(?Quest $Quest): self
    {
        $this->Quest = $Quest;

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

    public function getClue(): ?string
    {
        return $this->clue;
    }

    public function setClue(?string $clue): self
    {
        $this->clue = $clue;

        return $this;
    }

    public function getImageClue(): ?string
    {
        return $this->ImageClue;
    }

    public function setImageClue(?string $ImageClue): self
    {
        $this->ImageClue = $ImageClue;

        return $this;
    }
}
