<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\QuestScoreRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestScoreRepository::class)]
class QuestScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Quest::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Quest:User'])]
    private $questId;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'questScores')]
    #[ORM\JoinColumn(nullable: false)]
    private $userId;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Quest:User'])]
    private $score;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Quest:User'])]
    private $finished;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestId(): ?Quest
    {
        return $this->questId;
    }

    public function setQuestId(?Quest $questId): self
    {
        $this->questId = $questId;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }
}
