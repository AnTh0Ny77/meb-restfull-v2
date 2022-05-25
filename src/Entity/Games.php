<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GamesRepository;
use App\Controller\FinishGameController;
use App\Controller\GetCoverGamesController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetUnlockedGamesController;
use Doctrine\Common\Collections\ArrayCollection;
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
            'path' => 'games/unlocked',
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
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'security' =>
                [['bearerAuth' => []]],
                'summary' => 'public - retrieves a single game',
            ],
            'normalization_context' => ['groups' => ['read:Game']]
        ], 'SetFinish' => [
            'pagination_enabeld' => false,
            'method' => 'put',
            'path' => 'games/{id}/finish',
            'security' => 'is_granted("ROLE_USER")',
            'controller' => FinishGameController::class,
            'openapi_context' => [
                'security' =>
                [['bearerAuth' => []]],
                'summary' => 'j’arrête l’aventure json vide en body svp ',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                            ],
                        ],
                    ],
                ],
                "responses" => [
                    "200" => [
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
            ],
            'normalization_context' => ['groups' => ['read:Game']]
        ], "GetCover" => [
            'method' => 'Get',
            'path' => 'games/{id}/cover',
            'deserialize' => false,
            'controller' => GetCoverGamesController::class,
            'openapi_context' => [
                'summary'     => 'public request Get the game s cover',
                'description' => '',
                "responses" => [
                    "200" => [
                        "description" => "file",
                        "content" => [
                            "text/plain" => [
                                "schema" =>  []
                            ]
                        ]
                    ],
                ]
            ],
        ],
    ],
    normalizationContext: ['groups' => ['read:Games'], "enable_max_depth" => true]
)]
class Games
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([ 'read:Games' , 'read:Game'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Games' , 'read:Game'])]
    private $name;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['read:Games' , 'read:Game'])]
    private $destination;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $coverPath;

    #[Groups(['read:Games', 'read:Game'])]
    private $coverUrl;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Quest::class, orphanRemoval: true)]
    #[Groups(['read:Games', 'read:Game'])]
    private $quests;

    #[ORM\ManyToMany(targetEntity: BagTools::class, mappedBy: 'Games')]
    private $bagTools;

    public function __construct()
    {
        $this->quests = new ArrayCollection();
        $this->bagTools = new ArrayCollection();
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

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getCoverPath(): ?string
    {
        return $this->coverPath;
    }

    public function getCoverUrl()
    {
        if (!empty($this->getCoverPath())) {
            $this->coverUrl = 'api/games/' . $this->getId() . '/cover';
            return $this->coverUrl;
        }
        return null;
    }

    public function setCoverPath(?string $coverPath): self
    {
        $this->coverPath = $coverPath;

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

    /**
     * @return Collection<int, BagTools>
     */
    public function getBagTools(): Collection
    {
        return $this->bagTools;
    }

    public function addBagTool(BagTools $bagTool): self
    {
        if (!$this->bagTools->contains($bagTool)) {
            $this->bagTools[] = $bagTool;
            $bagTool->addGame($this);
        }

        return $this;
    }

    public function removeBagTool(BagTools $bagTool): self
    {
        if ($this->bagTools->removeElement($bagTool)) {
            $bagTool->removeGame($this);
        }

        return $this;
    }

    
}
