<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\QrCodeRepository;
use App\Controller\CreateQrController;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QrCodeRepository::class)]
#[ApiResource(
    collectionOperations: [
        'create' => [
            'path' => 'qr/create',
            'pagination_enabeld' => false,
            'controller' => CreateQrController::class,
            'method' => 'post',
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                [
                                    'game' => ['type' => 'string'],
                                ],
                            ],
                            'example' => [
                                'game'     => '1',
                            ],
                        ],
                    ]
                ],
                'description' => 'simule la creation d un qrcode et renvoi son contenu',
                'security' => [['bearerAuth' => []]],
                'summary' => 'available only during development',
                "responses" => [
                    "201" => [
                        "description" => "The Qrcode has been created",
                        "content" => [
                            "application/json" => [
                                "schema" =>  [
                                    "properties" => [
                                        "url" => [
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
        ],
    ],
    itemOperations: [
     
    ]
)]
class QrCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Client:User'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $idClient;

    /**
     * @Assert\Length(
     *      min = 30,
     *      max = 100,
     *      minMessage = "Your secret must be at least {{ limit }} characters long",
     *      maxMessage = "Your secret cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank
     */
    #[ORM\Column(type: 'string', length: 255 , unique: true)]
    private $secret;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Client:User'])]
    private $qrLock;

    #[ORM\ManyToOne(targetEntity: Games::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Client:User'])]
    private $idGame;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read:Client:User'])]
    private $time;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Client:User'])]
    private $createdAt;


    public function __construct()
    {
         $this->createdAt = new \DateTime("now");
        $this->createdAt->setTime($this->createdAt->format('H'), 0, 0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClient(): ?user
    {
        return $this->idClient;
    }

    public function setIdClient(?user $idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getQrLock(): ?bool
    {
        return $this->qrLock;
    }

    public function setQrLock(bool $qrLock): self
    {
        $this->qrLock = $qrLock;

        return $this;
    }

    public function getIdGame(): ?Games
    {
        return $this->idGame;
    }

    public function setIdGame(?Games $idGame): self
    {
        $this->idGame = $idGame;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

   
}
