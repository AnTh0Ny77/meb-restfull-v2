<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Controller\PostGuestController;
use App\Controller\ConfirmGuestController;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Controller\MeController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
        collectionOperations: [
            'me' => [
                'pagination_enabeld' => false,
                'path' => '/me',
                'method' => 'get',
                'controller' => MeController::class,
                'read' => false,
                'openapi_context' => [
                    'security' => [['bearerAuth' => []]]
                ]
            ], 
            'postGuest' => [
                'pagination_enabeld' => false,
                'path' => 'user/guest',
                'method' => 'post',
                'controller' => PostGuestController::class,
                'read' => true,
                'openapi_context' => [
                    'summary' => 'create a guest user',
                    'description' => 'The response contains the username and a connection link for the guest it can only be used once and its lifespan is 300 seconds, be careful to keep the refresh token. ',
                    'requestBody' => [
                       
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
                        ]
                    ]
                ]
            ]
        ],
        itemOperations: [ 
            'get' => [
                'controller' => NotFoundAction::class ,
                'read' => false ,
                'output' => false
            ]
        ]
)]
/**
* @UniqueEntity( "email" )
* @UniqueEntity( "username" )
*/
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface , JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
    * @Assert\Email(
    *     message = "The email '{{ value }}' is not a valid email."
    * )
    */
    #[ORM\Column(type: 'string', length: 180, unique: true , nullable: true)]
    private $email;

    #[ORM\Column(type: 'json')]
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
    public $username;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'boolean' )]
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

   

    public function __construct()
    {
        $this->secret = new ArrayCollection();
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

  

}
