<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal implements CRMEntityInterface
{
    /**
     * @var integer|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Název je povinné pole")]
    #[Assert\Length(max: 255, maxMessage: "Maximální délka názvu je 255 znaků")]
    private string $name;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "numeric", message: "Cena {{ value }} není ve správném formátu")]
    private ?float $price = null;

    /**
     * @var Section|null
     */
    #[ORM\ManyToOne(inversedBy: 'meals', fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Sekce je povinné pole")]
    private ?Section $section = null;

    /**
     * @var Course
     */
    #[ORM\ManyToOne(inversedBy: 'meals', fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Chod je povinné pole")]
    private Course $course;

    /**
     * @var boolean|null
     */
    #[ORM\Column(nullable: true)]
    private ?bool $invisible = null;

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return self
     */
    public function resetId(): self
    {
        $this->id = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return self
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param Section $section
     * @return self
     */
    public function setSection(Section $section): self
    {
        $this->section?->removeMeal($this);

        $this->section = $section;

        $this->section->addMeal($this);

        return $this;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     * @return self
     */
    public function setCourse(Course $course): self
    {
        $this->course->removeMeal($this);

        $this->course = $course;

        $this->course->addMeal($this);

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isInvisible(): ?bool
    {
        return $this->invisible;
    }

    /**
     * @param boolean|null $invisible
     * @return self
     */
    public function setInvisible(?bool $invisible): self
    {
        $this->invisible = $invisible;

        return $this;
    }
}
