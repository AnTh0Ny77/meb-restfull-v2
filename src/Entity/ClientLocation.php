<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use App\Repository\BagToolsRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ClientLocationRepository;
use App\Controller\ClientLocationController;

#[ORM\Entity(repositoryClass: ClientLocationRepository::class)]
#[ORM\Table(name: "client_location")]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/clientLocation'
        ] ,'PostClientLocation' => [
                    'path' => '/clientLocation',
                    'method' => 'post',
                    'controller' => ClientLocationController::class,
                    'deserialize' => false 
            ]
    ],
    itemOperations: [
         'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'path' => '/clientLocation/{id}'
        ] , 
        'delete' => [
            'pagination_enabeld' => false,
            'method' => 'delete',
            'path' => '/clientLocation/{id}'
        ]
    ])]
#[ApiFilter(OrderFilter::class, properties: ['booleanColumn' => 'desc' , 'id' => 'asc'])]
class ClientLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "clientLocations")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: "text", nullable: true)]
    private $textColumn;

    #[ORM\Column(type: "json", nullable: true)]
    private $jsonColumn;

    #[ORM\Column(type: "text", nullable: true)]
    private $postal;

    #[ORM\Column(type: "boolean", name: "boolean_column" , nullable: true)]
    private $booleanColumn;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTextColumn(): ?string
    {
        return $this->textColumn;
    }

    public function setTextColumn(?string $textColumn): self
    {
        $this->textColumn = $textColumn;

        return $this;
    }

    public function getJsonColumn(): ?array
    {
        return $this->jsonColumn;
    }

    public function setJsonColumn(?array $jsonColumn): self
    {
        $this->jsonColumn = $jsonColumn;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getBooleanColumn(): ?bool
    {
        return $this->booleanColumn;
    }

    public function setBooleanColumn(?bool $booleanColumn): self
    {
        $this->booleanColumn = $booleanColumn;

        return $this;
    }

  
}
