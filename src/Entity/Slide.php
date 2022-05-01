<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\PlayController;
use App\Repository\SlideRepository;
use App\Controller\GetQCMPController;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Controller\GetCoverSlideController;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: SlideRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['Poi' => 'exact'])]
#[ApiResource(
    order: ["Step" => "ASC"],
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'security' => 'is_granted("ROLE_USER")',
            'normalization_context' => ['groups' => 'read:Slide'],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'retrieves a Slide collection',
            ]
        ], 'getOutLine' => [
            'pagination_enabeld' => false,
            'path' => 'slides/offline',
            'method' => 'get',
            'security' => 'is_granted("ROLE_USER")',
            'normalization_context' => ['groups' => 'read:Slide:Offline'],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'retrieves a Slide collection with response for Outline parts',
            ]
        ]
    ],itemOperations:[
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:One:Slide'],
            'openapi_context' => [
                'summary' => 'public - retrieves a single Slide ',
            ]
            ], "GetCover" => [
            'method' => 'Get',
            'path' => 'slides/{id}/cover',
            'deserialize' => false,
            'controller' => GetCoverSlideController::class,
            'openapi_context' => [
                'summary'     => 'public request Get the slide s cover',
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
         'getQCMP' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/slides/{id}/qcmp',
            'read' => true,
            'security' => 'is_granted("ROLE_USER")',
            'controller' => GetQCMPController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'retrieves the image for the slide qcm photo',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                [
                                    'index'  => ['type' => 'integer']
                                ],
                            ],
                            'example' => [
                                'index'        =>  1
                            ],
                        ],
                    ],
                ],
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
        , 'play' => [
            'pagination_enabeld' => false,
            'path' => '/slides/{id}/play',
            'controller' => PlayController::class,
            'deserialize' => false,
            'method' => 'post',
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'security' =>
                [['bearerAuth' => []]],
                'summary' => 'Play with the slide - Merci de lire la description',
                'description' => 'Requete un peu différente puisque je vérifie que le game est bien disponible pour l utilisateur voir unlock/games, normalement le slide n est 
                jouable qu une seule fois mais j ai desactivé le controle pour faciliter le developpement seul les slides de type QCM et question ouverte nécéssitent de passer la varialbe
                answer dans le body , pour les autres slides l APi ne tiendra pas compte du contenu du body donc merci de passer un json vide, les Defis photos ne peuvent pas etre joués
                par cette requete ', 
                'read' => false,
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                [
                                    'answer'  => ['type' => 'string']
                                ],
                            ],
                            'example' => [
                                'answer'        => 'Citron'
                            ],
                        ],
                    ],
                ], "responses" => [
                        "200" => [
                            "description" => "Slide complete",
                            "content" => [
                                "application/json" => [
                                    "schema" =>  [

                                        "properties" => [
                                            "message" => [
                                                "type" => "string"
                                            ],
                                            "score" => [
                                                "type" => "integer"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ], "201" => [
                        "description" => "Slide complete score created",
                        "schema" =>  [
                            "type" => "object",
                            "properties" => [
                                "message" => [
                                    "type" => "string"
                                ],
                                "score" => [
                                    "type" => "int"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
)]
class Slide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Slide'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Slide' , 'read:Slide:Offline' , 'read:Game' ])]
    private $Name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Slide' , 'read:Slide:Offline' , 'read:Game'])]
    private $Text;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline', 'read:Game'])]
    private $TextSuccess;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline' , 'read:Game'])]
    private $TextFail;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline' , 'read:Game'])]
    private $Time;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read:Slide' , 'read:Slide:Offline' , 'read:Game'])]
    private $Step;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline' ])]
    private $Response;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Slide', 'read:Slide:Offline' , 'read:Game'])]
    private $Penality;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline' , 'read:Game'])]
    private $CoverPath;

    #[ORM\ManyToOne(targetEntity: Poi::class, inversedBy: 'slides')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:One:Slide' , 'read:Slide:Offline'])]
    private $Poi;

    #[ORM\ManyToOne(targetEntity: TypeSlide::class, inversedBy: 'Slide')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Slide'])]
    private $typeSlide;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    #[Groups([ 'read:Slide:Offline'])]
    private $Solution;

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

    public function getText()
    {
        $type = $this->getTypeSlide();
        $pattern = ";";
        if ($type->getId() == 3) {
            $text =  explode(';',  $this->Text);
            return $text;
        }
        return $this->Text;
    }

    public function setText(?string $Text): self
    {
        $this->Text = $Text;

        return $this;
    }

    public function getTextSuccess(): ?string
    {
        return $this->TextSuccess;
    }

    public function setTextSuccess(?string $TextSuccess): self
    {
        $this->TextSuccess = $TextSuccess;

        return $this;
    }

    public function getTextFail(): ?string
    {
        return $this->TextFail;
    }

    public function setTextFail(?string $TextFail): self
    {
        $this->TextFail = $TextFail;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->Time;
    }

    public function setTime(?int $Time): self
    {
        $this->Time = $Time;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->Step;
    }

    public function setStep(?int $Step): self
    {
        $this->Step = $Step;

        return $this;
    }

    public function getResponse()
    {
        $type = $this->getTypeSlide();
        $pattern = ";";
        if ($type->getId() == 2){
            $Response =  explode(';',  $this->Response);
            shuffle($Response);
            return $Response  ;
        }elseif ($type->getId() == 4) {
            return null;
        }
        elseif ($type->getId() == 5) {
            $Response =  explode(';',  $this->Response);
            $temp = [];
            $i = 0;
            foreach ($Response as $key => $image) {
                $i ++;
               $temp[$i] = $image;
            }
            return $temp;
        }
        return $this->Response;
    }

    public function setResponse(?string $Response): self
    {
        $this->Response = $Response;

        return $this;
    }

    public function getPenality(): ?bool
    {
        return $this->Penality;
    }

    public function setPenality(bool $Penality): self
    {
        $this->Penality = $Penality;

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

    public function getPoi(): ?Poi
    {
        return $this->Poi;
    }

    public function setPoi(?Poi $Poi): self
    {
        $this->Poi = $Poi;

        return $this;
    }

    public function getTypeSlide(): ?TypeSlide
    {
        return $this->typeSlide;
    }

    public function setTypeSlide(?TypeSlide $typeSlide): self
    {
        $this->typeSlide = $typeSlide;

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->Solution;
    }

    public function setSolution(?string $Solution): self
    {
        $this->Solution = $Solution;

        return $this;
    }
}
