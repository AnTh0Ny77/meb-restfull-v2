<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\QuestRepository;
use App\Controller\GetQuestController;
use App\Controller\PlayQuestController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuestRepository::class)]
#[ApiResource(
    collectionOperations: [
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
        ], 'play' => [
                'pagination_enabeld' => false,
                'path' => '/quest/{id}/play',
                'controller' => PlayQuestController::class,
                'deserialize' => true,
                'method' => 'post',
                'security' => 'is_granted("ROLE_USER")',
                'openapi_context' => [
                    'security' =>
                    [['bearerAuth' => []]],
                    'summary' => '',
                    'read' => false,
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema'  => [
                                    'type'       => 'object',
                                    'properties' =>
                                    [
                                        'isAccepted'  => ['type' => 'boolean']
                                    ],
                                ],
                                'example' => [
                                    'isAccepted'        => true
                                ],
                            ],
                        ]
                    ]
                ]
        ]
    ]
)]
class Quest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Game' , 'read:Game:User'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Quest' , 'read:oneQuest' , 'read:Game' , 'read:Game:User'])]
    private $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['read:Quest', 'read:oneQuest' , 'read:Game' , 'read:Game:User'])]
    private $color;

    #[ORM\ManyToOne(targetEntity: Games::class, inversedBy: 'quests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Quest', 'read:oneQuest' ])]
    private $game;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:oneQuest', 'read:Game' , 'read:Game:User'])]
    private $responseQuest;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $textQuest;

    #[ORM\OneToMany(mappedBy: 'quest', targetEntity: Poi::class)]
    #[Groups([ 'read:oneQuest' , 'read:Game' , 'read:Game:User'])]
    private $poi;

    #[Groups(['read:Game:User'])]
    private $userQuestScore;

    #[Groups(['read:Game:User'])]
    private $userQuestFinished;

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
        return $this->responseQuest;
    }

    public function setResponseQuest(?string $responseQuest): self
    {
        $this->responseQuest = $responseQuest;

        return $this;
    }


     public function getTextQuest(): ?string
    {
        return $this->responseQuest;
    }

    public function setTextQuest(?string $responseQuest): self
    {
        $this->responseQuest = $responseQuest;

        return $this;
    }

    /**
     * Get the value of userQuestScore
     */ 
    public function getUserQuestScore()
    {
        return $this->userQuestScore;
    }

    /**
     * Set the value of userQuestScore
     *
     * @return  self
     */ 
    public function setUserQuestScore($userQuestScore)
    {
        $this->userQuestScore = $userQuestScore;

        return $this;
    }

    /**
     * Get the value of userQuestFinished
     */ 
    public function getUserQuestFinished()
    {
        return $this->userQuestFinished;
    }

    /**
     * Set the value of userQuestFinished
     *
     * @return  self
     */ 
    public function setUserQuestFinished($userQuestFinished)
    {
        $this->userQuestFinished = $userQuestFinished;

        return $this;
    }
}
