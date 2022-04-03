<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\UnlockGamesController;
use App\Repository\UnlockGamesRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UnlockGamesRepository::class)]
#[ApiResource(
        collectionOperations: [
            'me' => [
                'pagination_enabeld' => false,
                'path' => 'UnlockGames/me',
                'method' => 'get',
                'controller' => UnlockGamesController::class,
                'read' => false,
                'openapi_context' => [
                    'summary' => 'retrive all the unlock games for the current user',
                    'security' => [['bearerAuth' => []]]
                ]
            ],
            'unlock' => [     
                'method' => 'get',
                'path' => 'UnlockGame/unlock',
                'controller' => UnlockGamesController::class,
                'read' => true,
                'openapi_context' => [
                    'security' => [['bearerAuth' => []]],
                    'summary' => 'Unlock a game for the current user',
                    'description' => '',
                    "responses" => [
                        "201" => [
                            "description" => "The games has been unlocked",
                            "content" => [
                                "application/json" => [
                                    "schema" =>  [
                                        "properties" => [
                                            "message" => [
                                                "type" => "string"
                                            ],
                                            "username" => [
                                                "type" => "string"
                                            ],
                                            "link" => [
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
           
        ]
)]
class UnlockGames
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $idUser;

    #[ORM\Column(type: 'boolean')]
    private $finish;

    #[ORM\OneToOne(targetEntity: QrCode::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $qrCode;


    public function __construct()
    {
        $this->qrId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?user
    {
        return $this->idUser;
    }

    public function setIdUser(?user $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getFinish(): ?bool
    {
        return $this->finish;
    }

    public function setFinish(bool $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    /**
     * @return Collection<int, qrCode>
     */
    public function getQrId(): Collection
    {
        return $this->qrId;
    }

    public function getQrCode(): ?qrCode
    {
        return $this->qrCode;
    }

    public function setQrCode(qrCode $qrCode): self
    {
        $this->qrCode = $qrCode;

        return $this;
    }

   
}
