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
#[ApiFilter(SearchFilter::class, properties: ['Quest' => 'exact'])]
#[ApiResource(
    order: ["Step" => "ASC"],
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
                'summary' => 'public - retrieves a single Poi ',
            ]
        ], 'getClue' => [
            'pagination_enabeld' => false,
            'path' => '/Poi/{id}/Clue',
            'method' => 'get',
            'normalization_context' => ['groups' => 'read:Clue'],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'summary' => 'get the Clue',
            ]
        ],'getImageClue' => [
            'pagination_enabeld' => false,
            'path' => '/Poi/{id}/ImageClue',
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
    #[Groups(['read:Quest', 'read:oneQuest' , 'read:Poi' ])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi' , 'read:Clue'])]
    private $Name;

    #[ORM\Column(type: 'json')]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi'])]
    private $Latlng = [];

    #[ORM\ManyToOne(targetEntity: Quest::class, inversedBy: 'poi')]
    #[Groups(['read:Poi'])]
    private $Quest;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Poi'])]
    private $Text;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:Clue'])]
    private $clue;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $ImageClue;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[Groups(['read:Quest', 'read:oneQuest', 'read:Poi'])]
    private $Step;

    #[ORM\ManyToOne(targetEntity: TypePoi::class, inversedBy: 'Poi')]
    #[Groups(['read:Poi'])]
    private $typePoi;

    #[ORM\OneToMany(mappedBy: 'Poi', targetEntity: Slide::class)]
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
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getLatlng(): ?array
    {
        return $this->Latlng;
    }

    public function setLatlng(array $Latlng): self
    {
        $this->Latlng = $Latlng;

        return $this;
    }

    public function getQuest(): ?Quest
    {
        return $this->Quest;
    }

    public function setQuest(?Quest $Quest): self
    {
        $this->Quest = $Quest;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(?string $Text): self
    {
        $this->Text = $Text;

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
        return $this->Step;
    }

    public function setStep(?int $Step): self
    {
        $this->Step = $Step;

        return $this;
    }

    public function getTypePoi(): ?TypePoi
    {
        return $this->typePoi;
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
