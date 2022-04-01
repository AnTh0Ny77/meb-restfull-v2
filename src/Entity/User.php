<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Controller\PostGuestController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
        collectionOperations: [
            'postGuest' => [
                'pagination_enabeld' => false,
                'path' => 'user/guest',
                'method' => 'post',
                'controller' => PostGuestController::class,
                'read' => true,
                'openapi_context' => [
                    'summary' => 'create a guest user',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema'  => [
                                    'type'       => 'object',
                                    'properties' =>[
                                        'key'  => ['type' => 'integer']  
                                    ],
                                ],
                                'example' =>[
                                    'key'  => '12456456',
                                ],
                            ]
                        ]
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
        itemOperations: []
)]
/**
* @UniqueEntity( "email" )
* @UniqueEntity( "username" )
*/
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
    private $username;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'boolean' )]
    private $confirmed;

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
        return (string) $this->email;
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
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
}
