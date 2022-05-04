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
            'unlock' => [
                'pagination_enabeld' => false,  
                'method' => 'get',
                'path' => 'UnlockGame/unlock',
                'controller' => UnlockGamesController::class,
                'read' => true,
                'openapi_context' => [
                    "parameters" =>[
                        [
                            "name" => "secret",
                            "in" => "query",
                            "required" => true,
                            "type" => "string"
                        ]
                        ],
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

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $date;


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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

   
}
