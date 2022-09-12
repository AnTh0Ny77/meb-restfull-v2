<?php

namespace App\Entity;

use App\Entity\GameScore;
use App\Controller\MeController;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RankRepository;
use App\Repository\UserRepository;
use App\Controller\PutUserController;
use App\Controller\GetCoverController;
use App\Controller\CoverUserController;
use App\Controller\GetClientController;
use App\Controller\PostGuestController;
use App\Controller\DeleteUserController;
use App\Controller\ConfirmGuestController;
use App\Controller\GetScoreControllerClass;
use App\Controller\ResetPasswordController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UpdatePasswordController;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
        collectionOperations: [
            'me' => [
                'pagination_enabeld' => false,
                'path' => 'user/me',
                'method' => 'get',
                'controller' => MeController::class,
                'normalization_context' => ['groups' => 'read:User'],
                'read' => false,
                'openapi_context' => [
                    'security' => [['bearerAuth' => []]]
                ]
                ],
            'getScore' => [
                'pagination_enabeld' => false,
                'path' => 'user/scores',
                'method' => 'get',
                'controller' => GetScoreControllerClass::class,
                'read' => false,
            ], 
            'ResetPassword' => [
                'pagination_enabeld' => false,
                'path' => 'user/reset/password',
                'method' => 'post',
                'controller' => ResetPasswordController::class,
                'normalization_context' => ['groups' => 'read:User'],
                'read' => false,
            'openapi_context' => [
                'summary'     => 'Send a recovery link to the user',
                'description' => '',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>[
                                    'email'  => ['type' => 'string']
                                ],
                            ],
                            'example' => [
                                'email'        => 'johndoe@yahoo.fr'
                            ]
                        ],
                    ],
                ],
                "responses" => [
                    "200" => [
                        "description" => "user has been updated",
                        "content" => [
                            "application/json" => [
                                "schema" =>  [
                                    "properties" => [
                                        "message" => [
                                            "type" => "string"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ], "201" => [
                        "description" => "user has been updated",
                        "content" => [
                            "application/json" => [
                                "schema" =>  [
                                    "properties" => [
                                        "message" => [
                                            "type" => "string"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
            ],
            'postGuest' => [
                'pagination_enabeld' => false,
                'path' => 'user/guest',
                'method' => 'post',
                'controller' => PostGuestController::class,
                'read' => true,
                'openapi_context' => [
                    'summary' => 'create a guest user (merci de passer un objet json vide en body)',
                    'description' => 'The response contains the username and a connection link for the guest it can only be used once and its lifespan is 300 seconds, be careful to keep the refresh token. ',
                    'requestBody' => [
                    'content' => []
                    ],
                    "responses" => [
                        "201" => [
                            "description" => "The guest has been created",
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
               
            ],
           
            "confirm" => [
                'method' => 'put',
                'path' => 'user/guest/confirm',
                'controller' => ConfirmGuestController::class,
                'openapi_context' => [
                    'security' =>
                    [['bearerAuth' => []]],
                    'summary'     => 'Update de guest account to classic user account',
                    'description' => '',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema'  => [
                                    'type'       => 'object',
                                    'properties' =>
                                    [
                                        'username' => ['type' => 'string'],
                                        'email'  => ['type' => 'string'] , 
                                        'password' => ['type' => 'string'] , 
                                        'name' =>  ['type' => 'string']  ,
                                        'firstname' => ['type' => 'string'] 
                                    ],
                                ],
                                'example' => [
                                    'username'     => '3xCh4ng3',
                                    'email'        => 'johndoe@yahoo.fr',
                                    'password'  => 'securiTYY1234',
                                    'name' => 'Doe',
                                    'firstname' => 'john'
                                ],
                            ],
                        ], 
                        "responses" => [
                            "201" => [
                                "description" => "user has been updated",
                                "content" => [
                                    "application/json" => [
                                        "schema" =>  [
                                            "properties" => [
                                                "message" => [
                                                    "type" => "string"
                                                ] 
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
            ], 'cover' => [
            'method' => 'post',
            'path' => 'user/cover',
            'deserialize' => false,
            'controller' => CoverUserController::class,
            'openapi_context' => [
                'security' =>
                [['bearerAuth' => []]],
                'summary'     => 'Post the user cover image ( need an definitive account : api/user/guest/confirm ) please use : multipart/form-data',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'cover' => [
                                        'type' => 'string',
                                        'format' => 'biniray'
                                    ]
                                ]
                            ]
                        ]
                    ]

                ], "responses" => [
                    "201" => [
                        "description" => "cover has been updated",
                        "content" => [
                            "application/json" => [
                                "schema" =>  [
                                    "properties" => [
                                        "message" => [
                                            "type" => "string"
                                        ],
                                        "cover" => [
                                            "type" => "string"
                                        ]
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
            // 'get' => [
            //     'controller' => NotFoundAction::class ,
            //     'read' => false ,
            //     'output' => false
            // ],
            "putUser" => [
                'method' => 'PUT',
                'path' => 'user/{id}/update',
                'deserialize' => false,
                'controller' => PutUserController::class,
                'openapi_context' => [
                    'security' =>
                    [['bearerAuth' => []]],
                    'summary'     => 'Update the current user',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                [
                                    'username' => ['type' => 'string'],
                                    'email'  => ['type' => 'string'],
                                    'name' =>  ['type' => 'string'],
                                    'firstname' => ['type' => 'string']
                                ],
                            ],
                            'example' => [
                                    'username'     => '3xCh4ng3',
                                    'email'        => 'johndoe@yahoo.fr',
                                    'name' => 'Doe',
                                    'firstname' => 'john'
                            ],
                            ]
                        ]
                    ], "responses" => [
                        "201" => [
                            "description" => "user has been updated",
                            "content" => [
                                "application/json" => [
                                    "schema" =>  [
                                        "properties" => [
                                            "message" => [
                                                "type" => "string"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "401" => [
                            "description" => "invalid request"
                        ]
                    ]
                ],
            ], 
            "GetCover" => [
                'method' => 'Get',
                'path' => 'user/{id}/cover',
                'deserialize' => false,
                'controller' => GetCoverController::class,
                'openapi_context' => [
                    'security' =>
                    [['bearerAuth' => []]],
                    'summary'     => 'Get the current user s cover',
                     'description' => '',
                        "responses" => [
                            "200" => [
                                "description" => "file",
                                "content" => [
                                    "text/plain" => [
                                        "schema" =>  [
                                            
                                        ]
                                    ]
                                ]
                            ],
                        ]
                ],
            ], 'poiScore' => [
                'pagination_enabeld' => false,
                'path' => 'user/{id}/poi/score',
                'deserialize' => true,
                'read' =>  true ,
                'method' => 'get',
                'normalization_context' => ['groups' => 'read:Poi:User'],
                'openapi_context' => [
                    'security' => [['bearerAuth' => []]]
                ]
            ], 'QuestScore' => [
                'pagination_enabeld' => false,
                'path' => 'user/{id}/quest/score',
                'deserialize' => true,
                'read' =>  true,
                'method' => 'get',
                'normalization_context' => ['groups' => 'read:Quest:User'],
                'openapi_context' => [
                    'security' => [['bearerAuth' => []]]
                ]
        ], 'DeleteUser' => [
            'pagination_enabeld' => false,
            'path' => 'user/{id}/delete',
            'controller' => DeleteUserController::class,
            'read' =>  true,
            'method' => 'delete',
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ]
        ], 'getClient' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/user/{id}/client',
            'controller' => GetClientController::class,
            'security' => 'is_granted("ROLE_CLIENT")',
            'openapi_context' => [
                'security' =>
                [['bearerAuth' => []]],
                'summary' => 'Client - retrieves a client data ( need a client role )',
            ],
            'normalization_context' => ['groups' => ['read:Client:User']]
        ],
            'updatePassword' =>[
                'pagination_enabeld' => false,
                'deserialize' => false,
                'path' => '/user/{id}/password',
                'method' => 'PUT',
                'controller' => UpdatePasswordController::class,
                'openapi_context' => [
                    'summary' => 'Update the user password',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema'  => [
                                    'type'       => 'object',
                                    'properties' =>
                                    [
                                        'password'  => ['type' => 'string'],
                                        'actual_password' => ['type' => 'string']
                                    ],
                                ],
                                'example' => [
                                    'password'  => 'Security1234',
                                    'actual_password' =>  'Security123'
                                ],
                            ]
                        ]
                    ], "responses" => [
                        "200" => [
                            "description" => "Password Updated",
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
                        "201" => [
                            "description" => "Password Updated",
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
                        ]   
                    ]
                ] 
            ]
        ]
)]
/**
* @UniqueEntity( "email" )
* @UniqueEntity( "username" )
* @Vich\Uploadable
*/
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface , JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $id;

    /**
    * @Assert\Email(
    *     message = "The email '{{ value }}' is not a valid email."
    * )
    */
    #[ORM\Column(type: 'string', length: 180, unique: true , nullable: true)]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $roles = [];

    #[ORM\Column(type: 'string' , nullable: true)]
    private $password;

    /**
    * @Assert\Length(
    *      min = 3,
    *      max = 70,
    *      minMessage = "Your pseudo must be at least {{ limit }} characters long",
    *      maxMessage = "Your pseudo cannot be longer than {{ limit }} characters"
    * )
    * @Assert\NotBlank
    */
    #[ORM\Column(type: 'string', length: 255 , unique: true)]
    #[Groups(['read:User' , 'read:Client:User'])]
    public $username;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $updatedAt;

    #[ORM\Column(type: 'boolean' )]
    #[Groups(['read:User'])]
    private $confirmed;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 70,
     *      minMessage = "Your name must be at least {{ limit }} characters long",
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters"
     * )
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $name;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 70,
     *      minMessage = "Your name must be at least {{ limit }} characters long",
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters"
     * )
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $firstName;

    /**
     * @SerializedName("password")
     * @Assert\Regex(
     *     pattern="/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/",
     *     match=true,
     *     message="Your password must be : 8 characters minimun . must contain 1 number , 1 uppercase  and 1 lowercase character "
     * )
     */
    private $PlainPassword;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:User', 'read:Client:User'])]
    private $coverPath;

    /**
     * @var File|null
     * @Assert\File(
     *     maxSize = "64M",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Please upload a valid cover image: jpeg or png under 64M")
     * @Vich\UploadableField(mapping="user_cover", fileNameProperty="coverPath")
     */
    
    private $file;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Score::class)]
    private $scores;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: QuestScore::class)]
    #[Groups(['read:Quest:User'])]
    private $questScores;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: PoiScore::class)]
    #[Groups(['read:Poi:User'])]
    private $poiScores;

    

    #[ORM\ManyToOne(targetEntity: Rank::class, inversedBy: 'User')]
    #[Groups(['read:User'])]
    private $rank;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:User' , 'read:Client:User'])]
    private $phone;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: GameScore::class, orphanRemoval: true)]
    private $GameScore;

    #[ORM\Column(type: 'json', nullable: true)]
    private $location = [];


    #[ORM\OneToMany(mappedBy: 'User', targetEntity: ClientGames::class)]
    #[Groups(['read:Client:User'])]
    private $clientGames;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Client:User'])]
    private $clientInfiniteQr;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['read:Client:User'])]
    private $exploreCoin;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read:Client:User'])]
    private $bagNumber;

    public function __construct()
    {
        
        $this->createdAt = new \DateTime("now");
        $this->secret = new ArrayCollection();
        $this->scores = new ArrayCollection();
        $this->questScores = new ArrayCollection();
        $this->poiScores = new ArrayCollection();
        $this->Game = new ArrayCollection();
        $this->clientGames = new ArrayCollection();
    }


    // public function __construct($username, array $roles, $email)
    // {
    //     $this->username = $username;
    //     $this->roles = $roles;
    //     $this->email = $email;
    // }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public static function createFromPayload($username, array $payload)
    {
       $user = new User();
       $user->setUsername($username);
       return $user;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->PlainPassword;
    }

    public function setPlainPassword(string $PlainPassword): self
    {
        $this->PlainPassword = $PlainPassword;

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
     * Get maxSize = "2048k",
     *
     * @return  File|null
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set maxSize = "2048k",
     *
     * @param  File|null  $file  maxSize = "2048k",
     *
     * @return  self
     */ 
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection<int, Score>
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(Score $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores[] = $score;
            $score->setUser($this);
        }

        return $this;
    }

    public function removeScore(Score $score): self
    {
        if ($this->scores->removeElement($score)) {
            // set the owning side to null (unless already changed)
            if ($score->getUser() === $this) {
                $score->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuestScore>
     */
    public function getQuestScores(): Collection
    {
        return $this->questScores;
    }

    public function addQuestScore(QuestScore $questScore): self
    {
        if (!$this->questScores->contains($questScore)) {
            $this->questScores[] = $questScore;
            $questScore->setUserId($this);
        }

        return $this;
    }

    public function removeQuestScore(QuestScore $questScore): self
    {
        if ($this->questScores->removeElement($questScore)) {
            // set the owning side to null (unless already changed)
            if ($questScore->getUserId() === $this) {
                $questScore->setUserId(null);
            }
        }

        return $this;
    }

    private $forgotPasswordToken;

    private $forgotPasswordTokenRequestedAt;
    
    private $forgotPasswordTokenMustBeVerifiedBefore;



    /**
     * Get the value of forgotPasswordToken
     */ 
    public function getForgotPasswordToken()
    {
        return $this->forgotPasswordToken;
    }

    /**
     * Set the value of forgotPasswordToken
     *
     * @return  self
     */ 
    public function setForgotPasswordToken($forgotPasswordToken)
    {
        $this->forgotPasswordToken = $forgotPasswordToken;

        return $this;
    }

    /**
     * Get the value of forgotPasswordTokenRequestedAt
     */ 
    public function getForgotPasswordTokenRequestedAt()
    {
        return $this->forgotPasswordTokenRequestedAt;
    }

    /**
     * Set the value of forgotPasswordTokenRequestedAt
     *
     * @return  self
     */ 
    public function setForgotPasswordTokenRequestedAt($forgotPasswordTokenRequestedAt)
    {
        $this->forgotPasswordTokenRequestedAt = $forgotPasswordTokenRequestedAt;

        return $this;
    }

   

   

    /**
     * Get the value of forgotPasswordTokenMustBeVerifiedBefore
     */ 
    public function getForgotPasswordTokenMustBeVerifiedBefore()
    {
        return $this->forgotPasswordTokenMustBeVerifiedBefore;
    }

    /**
     * Set the value of forgotPasswordTokenMustBeVerifiedBefore
     *
     * @return  self
     */ 
    public function setForgotPasswordTokenMustBeVerifiedBefore($forgotPasswordTokenMustBeVerifiedBefore)
    {
        $this->forgotPasswordTokenMustBeVerifiedBefore = $forgotPasswordTokenMustBeVerifiedBefore;

        return $this;
    }

    /**
     * @return Collection<int, PoiScore>
     */
    public function getPoiScores(): Collection
    {
        return $this->poiScores;
    }

    public function addPoiScore(PoiScore $poiScore): self
    {
        if (!$this->poiScores->contains($poiScore)) {
            $this->poiScores[] = $poiScore;
            $poiScore->setUser($this);
        }

        return $this;
    }

    public function removePoiScore(PoiScore $poiScore): self
    {
        if ($this->poiScores->removeElement($poiScore)) {
            // set the owning side to null (unless already changed)
            if ($poiScore->getUser() === $this) {
                $poiScore->setUser(null);
            }
        }

        return $this;
    }

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function setRank(?Rank $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, GameScore>
     */
    public function getGame(): Collection
    {
        return $this->GameScore;
    }

    public function addGame(GameScore $GameScore): self
    {
        if (!$this->GameScore->contains($GameScore)) {
            $this->GameScore[] = $GameScore;
            $GameScore->setUser($this);
        }

        return $this;
    }

    public function removeGame(GameScore $GameScore): self
    {
        if ($this->GameScore->removeElement($GameScore)) {
            // set the owning side to null (unless already changed)
            if ($GameScore->getUser() === $this) {
                $GameScore->setUser(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?array
    {
        return $this->location;
    }

    public function setLocation(?array $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, ClientGames>
     */
    public function getClientGames(): Collection
    {
        return $this->clientGames;
    }

    public function addClientGame(ClientGames $clientGame): self
    {
        if (!$this->clientGames->contains($clientGame)) {
            $this->clientGames[] = $clientGame;
            $clientGame->setUser($this);
        }

        return $this;
    }

    public function removeClientGame(ClientGames $clientGame): self
    {
        if ($this->clientGames->removeElement($clientGame)) {
            // set the owning side to null (unless already changed)
            if ($clientGame->getUser() === $this) {
                $clientGame->setUser(null);
            }
        }

        return $this;
    }

    public function getClientInfiniteQr(): ?bool
    {
        return $this->clientInfiniteQr;
    }

    public function setClientInfiniteQr(bool $clientInfiniteQr): self
    {
        $this->clientInfiniteQr = $clientInfiniteQr;

        return $this;
    }

    public function getExploreCoin(): ?float
    {
        return $this->exploreCoin;
    }

    public function setExploreCoin(?float $exploreCoin): self
    {
        $this->exploreCoin = $exploreCoin;

        return $this;
    }

    public function getBagNumber(): ?int
    {
        return $this->bagNumber;
    }

    public function setBagNumber(?int $bagNumber): self
    {
        $this->bagNumber = $bagNumber;

        return $this;
    }

    
}
