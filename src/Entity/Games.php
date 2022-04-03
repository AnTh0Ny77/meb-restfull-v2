<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GamesRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetUnlockedGamesController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'openapi_context' => [
                'summary' => 'public - retrieves a games collection  ',
            ],
            'normalization_context' => ['groups' => ['read:Games']]
        ],
        'unlock' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => 'game/unlocked',
            'controller' => GetUnlockedGamesController::class,
            'read' => true,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'retrieve a collection of unlocked games for the current user ',
                'description' => '',
                "responses" => [
                    "201" => [
                        "content" => [
                            "application/json" => [
                                "schema" =>  [
                                    "properties" => [
                                        "response" => [
                                            "type" => "string"
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "401" => [
                        "description" => "invalid request"
                    ]
                ]

            ]
        ]
    ],
    itemOperations: [ 
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'openapi_context' => [
                'summary' => 'public - retrieves a single game',
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

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Quest::class, orphanRemoval: true)]
    private $quests;

    public function __construct()
    {
        $this->quests = new ArrayCollection();
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

    /**
     * @return Collection<int, Quest>
     */
    public function getQuests(): Collection
    {
        return $this->quests;
    }

    public function addQuest(Quest $quest): self
    {
        if (!$this->quests->contains($quest)) {
            $this->quests[] = $quest;
            $quest->setGame($this);
        }

        return $this;
    }

    public function removeQuest(Quest $quest): self
    {
        if ($this->quests->removeElement($quest)) {
            // set the owning side to null (unless already changed)
            if ($quest->getGame() === $this) {
                $quest->setGame(null);
            }
        }

        return $this;
    }
}
