<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetQuestController;
use App\Repository\QuestRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestRepository::class)]
#[ApiResource(
    collectionOperations: [
        // 'GetGames' => [
        //     'pagination_enabeld' => false,
        //     'controller' => GetQuestController::class,
        //     'method' => 'get',
        //     'openapi_context' => [
        //         "parameters" => [
        //             [
        //                 "name" => "game_id",
        //                 "in" => "query",
        //                 "required" => true,
        //                 "type" => "integer"
        //             ]
        //         ],
        //         'security' => [['bearerAuth' => []]],
        //         'summary' => 'retrieves a Quest collection with the game id ( the game must have been unlocked )',
        //         "responses" => [
        //             "200" => [
        //                 "content" => [
        //                     "application/json" => [
        //                         "schema" =>  [
        //                             "properties" => [
        //                                 "quest" => [
        //                                     "type" => "string"
        //                                 ]
        //                             ]
        //                         ]
        //                     ]
        //                 ]
        //             ],
        //             "401" => [
        //                 "description" => "invalid request"
        //             ]
        //         ]
        //     ]
        // ], 
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Quest'],
            'openapi_context' => [
                'summary' => 'public - retrieves a Quest collection  ',
            ]
        ]
    ],
    itemOperations: [ 
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:oneQuest'],
            'openapi_context' => [
                'summary' => 'public - retrieves a single Quest   ',
            ]
        ]
    ]
)]
class Quest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Game'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Quest' , 'read:oneQuest' , 'read:Game'])]
    private $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['read:Quest', 'read:oneQuest' , 'read:Game'])]
    private $color;

    #[ORM\ManyToOne(targetEntity: Games::class, inversedBy: 'quests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Quest', 'read:oneQuest'])]
    private $game;

    #[ORM\OneToMany(mappedBy: 'quest', targetEntity: Poi::class)]
    #[Groups([ 'read:oneQuest' , 'read:Game'])]
    private $poi;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ResponseQuest;

    public function __construct()
    {
        $this->poi = new ArrayCollection();
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

    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection<int, Poi>
     */
    public function getPoi(): Collection
    {
        return $this->poi;
    }

    public function addPoi(Poi $poi): self
    {
        if (!$this->poi->contains($poi)) {
            $this->poi[] = $poi;
            $poi->setQuest($this);
        }

        return $this;
    }

    public function removePoi(Poi $poi): self
    {
        if ($this->poi->removeElement($poi)) {
            // set the owning side to null (unless already changed)
            if ($poi->getQuest() === $this) {
                $poi->setQuest(null);
            }
        }

        return $this;
    }

    public function getResponseQuest(): ?string
    {
        return $this->ResponseQuest;
    }

    public function setResponseQuest(?string $ResponseQuest): self
    {
        $this->ResponseQuest = $ResponseQuest;

        return $this;
    }
}
