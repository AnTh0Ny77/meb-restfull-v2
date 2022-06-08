<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RankRepository;
use App\Controller\GetCoverRankController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RankRepository::class)]
#[ORM\Table(name: 'ranks')]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/ranks',
            'normalization_context' => ['groups' => 'read:ranks'],
            'openapi_context' => [
                'summary' => 'retrieves a Rank collection  ',
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/rank/{id}',
            'normalization_context' => ['groups' => 'read:ranks'],
            'openapi_context' => [
                'summary' => 'retrieves a single Rank ',
            ]
        ], 'getCover' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/rank/{id}/cover',
            'read' => true,
            'normalization_context' => ['groups' => 'read:ranks'],
            'controller' => GetCoverRankController::class,
            'openapi_context' => [
                'summary' => 'retrieves the cover of the Rank ',
                "responses" => [
                    "200" => [
                        "description" => "file",
                        "content" => [
                            "text/plain" => [
                                "schema" =>  []
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
)]
class Rank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:User' , 'read:ranks' ])]
    private $id;

    #[ORM\OneToMany(mappedBy: 'rank', targetEntity: User::class)]
    private $User;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:User' , 'read:ranks'])]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[Groups([ 'read:ranks'])]
    private $unlockScore;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $coverPath;

    #[Groups(['read:User' , 'read:ranks'])]
    private $coverUrl;

    public function __construct()
    {
        $this->User = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
            $user->setRank($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRank() === $this) {
                $user->setRank(null);
            }
        }

        return $this;
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

    public function getUnlockScore(): ?int
    {
        return $this->unlockScore;
    }

    public function setUnlockScore(int $unlockScore): self
    {
        $this->unlockScore = $unlockScore;

        return $this;
    }

    public function getCoverPath(): ?string
    {
        return $this->coverPath;
    }

    public function setCoverPath(?string $coverPath): self
    {
        $this->coverPath = $coverPath;

        return $this;
    }

    /**
     * Get the value of coverUrl
     */ 
    public function getCoverUrl()
    {
        if (!empty($this->getCoverPath())) {
            $this->coverUrl = 'api/rank/' . $this->getId() . '/cover';
            return $this->coverUrl;
        }
    }

    /**
     * Set the value of coverUrl
     *
     * @return  self
     */ 
    public function setCoverUrl($coverUrl)
    {
        $this->coverUrl = $coverUrl;

        return $this;
    }
}
