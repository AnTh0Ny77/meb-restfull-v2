<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PoiRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Controller\GetImageClueController;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: PoiRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['quest' => 'exact'])]
#[ApiResource(
    order: ["step" => "ASC"],
    collectionOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Poi'],
            'openapi_context' => [
                'summary' => 'public - retrieves a Poi collection  ',
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'pagination_enabeld' => false,
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Poi'],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'public - retrieves a single Poi ',
            ]
        ], 'getClue' => [
            'pagination_enabeld' => false,
            'path' => '/poi/{id}/clue',
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Clue'],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'get the Clue',
            ]
        ],'getImageClue' => [
            'pagination_enabeld' => false,
            'path' => '/Poi/{id}/imageClue',
            'controller' => GetImageClueController::class,
            'read' => true,
            'method' => 'get',
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'get the image Clue',
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
            ]
        ]
    ]
)]
class Poi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Quest', 'read:oneQuest' , 'read:Poi' , 'read:Game'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi' , 'read:Clue' , 'read:Game'])]
    private $name;

    #[ORM\Column(type: 'json')]
    private $latlng = [];

    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi', 'read:Game'])]
    private $lat;

    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi', 'read:Game'])]
    private $lng;

    #[ORM\ManyToOne(targetEntity: Quest::class, inversedBy: 'poi')]
    #[Groups(['read:Poi' , ])]
    private $quest;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Poi' , 'read:Game'])]
    private $text;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Clue' , 'read:Game'])]
    private $clue;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $ImageClue;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi' , 'read:Game'])]
    private $step;

    #[ORM\ManyToOne(targetEntity: TypePoi::class, inversedBy: 'Poi')]
    private $typePoi;

    #[Groups(['read:Poi', 'read:Game'])]
    private $typePoiId;

    #[ORM\OneToMany(mappedBy: 'poi', targetEntity: Slide::class)]
    #[Groups([ 'read:Game' , 'read:Poi'])]
    private $slides;

  

    public function __construct()
    {
        $this->slides = new ArrayCollection();
    }

   
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

    
    public function getLatlng(): ?array
    {
        return $this->latlng;
    }

    
    public function getLat()
    {
        $array = $this->getLatlng();
        $this->lat = $array['lat'];
        return $this->lat;
    }

   
    public function getLng()
    {
        $array = $this->getLatlng();
        $this->lng = $array['lng'];
        return $this->lng;
    }

    public function setLatlng(array $latlng): self
    {
        $this->latlng = $latlng;

        return $this;
    }

    public function getQuest(): ?Quest
    {
        return $this->quest;
    }

    public function setQuest(?Quest $quest): self
    {
        $this->quest = $quest;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getClue(): ?string
    {
        return $this->clue;
    }

    public function setClue(?string $clue): self
    {
        $this->clue = $clue;

        return $this;
    }

    public function getImageClue(): ?string
    {
        return $this->ImageClue;
    }

    public function setImageClue(?string $ImageClue): self
    {
        $this->ImageClue = $ImageClue;

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

    public function getTypePoi(): ?TypePoi
    {
        return $this->typePoi;
    }

    public function getTypePoiId()
    {
        $this->typePoiId = $this->getTypePoi()->getId();
        return $this->typePoiId;
    }

    public function setTypePoi(?TypePoi $typePoi): self
    {
        $this->typePoi = $typePoi;

        return $this;
    }

    /**
     * @return Collection<int, Slide>
     */
    public function getSlides(): Collection
    {
        return $this->slides;
    }

    public function addSlide(Slide $slide): self
    {
        if (!$this->slides->contains($slide)) {
            $this->slides[] = $slide;
            $slide->setPoi($this);
        }

        return $this;
    }

    public function removeSlide(Slide $slide): self
    {
        if ($this->slides->removeElement($slide)) {
            // set the owning side to null (unless already changed)
            if ($slide->getPoi() === $this) {
                $slide->setPoi(null);
            }
        }

        return $this;
    }

   
}
