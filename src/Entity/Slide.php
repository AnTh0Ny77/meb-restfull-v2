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
#[ApiFilter(SearchFilter::class, properties: ['poi' => 'exact'])]
#[ApiResource(
    order: ["step" => "ASC"],
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
        ]
        // 'getOutLine' => [
        //     'pagination_enabeld' => false,
        //     'path' => 'slides/offline',
        //     'method' => 'get',
        //     'security' => 'is_granted("ROLE_USER")',
        //     'normalization_context' => ['groups' => 'read:Slide:Offline'],
        //     'openapi_context' => [
        //         'security' => [['bearerAuth' => []]],
        //         'summary' => 'retrieves a Slide collection with response for Outline parts',
        //     ]
        // ]
    ],itemOperations:[
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Slide'],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
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
                "parameters" => [
                       [
                          "name" => "index",
                           "in" => "query",
                          "description" => "index of response",
                           "required" => true,
                          "type" => "integer",
                          "items" => [
                               "type" => "integer"
                           ]
                       ]
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
                par cette requete . La variable isAccepted  n est nécéssaire que pour les types :  Question', 
                'read' => false,
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                [
                                    'answer'  => ['type' => 'string'],
                                    'isAccepted'  => ['type' => 'boolean']
                                ],
                            ],
                            'example' => [
                                'answer'        => 'Citron',
                                'isAccepted'        => true
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
    #[Groups(['read:Slide' , 'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Slide' , 'read:Slide:Offline' , 'read:Game' , 'read:Poi' , 'read:Game:User' ])]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Slide' , 'read:Slide:Offline' , 'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $text;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline', 'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $textSuccess;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline' , 'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $textFail;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $time;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read:Slide' , 'read:Slide:Offline' , 'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $step;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Slide', 'read:Slide:Offline' ,  'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $response;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Slide', 'read:Slide:Offline' , 'read:Game' , 'read:Poi' , 'read:Game:User'])]
    private $penality;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $coverPath;

    #[Groups(['read:Slide', 'read:Slide:Offline', 'read:Game', 'read:Poi' , 'read:Game:User'])]
    private $coverUrl;

    #[ORM\ManyToOne(targetEntity: Poi::class, inversedBy: 'slides')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:One:Slide' , 'read:Slide:Offline' , 'read:Poi'])]
    private $poi;

    #[ORM\ManyToOne(targetEntity: TypeSlide::class, inversedBy: 'Slide')]
    #[ORM\JoinColumn(nullable: false)]
    private $typeSlide;

    #[Groups(['read:Slide', 'read:Game', 'read:Poi' , 'read:Game:User'])]
    private $typeSlideId;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    #[Groups([ 'read:Slide:Offline' , 'read:Slide' , 'read:Game' , 'read:Poi',  'read:Game:User'])]
    private $solution;

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

    public function getText()
    {
        $type = $this->getTypeSlide();
        $pattern = ";";
        if ($type->getId() == 3) {
            $text =  explode(';',  $this->text);
            return $text;
        }
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTextSuccess(): ?string
    {
        return $this->textSuccess;
    }

    public function setTextSuccess(?string $textSuccess): self
    {
        $this->textSuccess = $textSuccess;

        return $this;
    }

    public function getTextFail(): ?string
    {
        return $this->textFail;
    }

    public function setTextFail(?string $textFail): self
    {
        $this->textFail = $textFail;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->Time = $time;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(?int $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getResponse()
    {
        $type = $this->getTypeSlide();
        $pattern = ";";
        if ($type->getId() == 2){
            $Response =  explode(';',  $this->response);
            shuffle($Response);
            return $Response  ;
        }elseif ($type->getId() == 4) {
            return null;
        }
        elseif ($type->getId() == 5) {
            $Response =  explode(';',  $this->response);
            $temp = [];
            $i = 0;
            foreach ($Response as $key => $image) {
                $i ++;
               $temp[$i] = 'api/slides/' .$this->getId() . '/qcmp?index=' . $i;
            }
            return $temp;
        }
        return $this->response;
    }
    
    public function getUrlQcmp(){
        $Response =  explode(';',  $this->response);
        $temp = [];
        $i = 0;
        foreach ($Response as $key => $image) {
            $i++;
            $temp[$i] = $image;
        }
        return $temp;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getPenality(): ?bool
    {
        return $this->penality;
    }

    public function setPenality(bool $penality): self
    {
        $this->penality = $penality;

        return $this;
    }

    public function getCoverPath(): ?string
    {
        return $this->coverPath;
    }

    public function getCoverUrl()
    {
        if (!empty($this->getCoverPath())){
            $this->coverUrl = 'api/slides/' . $this->getId() . '/cover';
            return $this->coverUrl;
        }
        return null;
    }

    public function setCoverPath(?string $coverPath): self
    {
        $this->coverPath = $coverPath;

        return $this;
    }

    public function getPoi(): ?Poi
    {
        return $this->poi;
    }

    public function setPoi(?Poi $poi): self
    {
        $this->poi = $poi;

        return $this;
    }

    public function getTypeSlide(): ?TypeSlide
    {
        return $this->typeSlide;
    }

    public function getTypeSlideId()
    {
        $this->typeSlideId = $this->getTypeSlide()->getId();
        return $this->typeSlideId;
    }

    public function setTypeSlide(?TypeSlide $typeSlide): self
    {
        $this->typeSlide = $typeSlide;

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(?string $solution): self
    {
        $this->solution = $solution;

        return $this;
    }

    
}
