<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetQuestController;
use App\Repository\QuestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestRepository::class)]
#[ApiResource(
    collectionOperations: [
        'GetGames' => [
            'pagination_enabeld' => false,
            'controller' => GetQuestController::class,
            'method' => 'get',
            'openapi_context' => [
                "parameters" => [
                    [
                        "name" => "game_id",
                        "in" => "query",
                        "required" => true,
                        "type" => "integer"
                    ]
                ],
                'security' => [['bearerAuth' => []]],
                'summary' => 'retrieves a Quest collection with the game id ( the game must have been unlocked )   ',
            ]
        ],
    ],
    itemOperations: [
       
    ],
    normalizationContext: ['groups' => ['read:Quest'], "enable_max_depth" => true]
)]
class Quest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $color;

    #[ORM\ManyToOne(targetEntity: Games::class, inversedBy: 'quests')]
    #[ORM\JoinColumn(nullable: false)]
    private $game;

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

    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $game): self
    {
        $this->game = $game;

        return $this;
    }
}
