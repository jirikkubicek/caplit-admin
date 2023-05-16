<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal implements CloneableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Název je povinné pole")]
    #[Assert\Length(max: 255, maxMessage: "Maximální délka názvu je 255 znaků")]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "numeric", message: "Cena {{ value }} není ve správném formátu")]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'meals', fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Sekce je povinné pole")]
    private ?Section $section = null;

    #[ORM\ManyToOne(inversedBy: 'meals', fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Chod je povinné pole")]
    private ?Course $course = null;

    #[ORM\Column(nullable: true)]
    private ?bool $invisible = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function resetId(): self
    {
        $this->id = null;

        return $this;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section?->removeMeal($this);

        $this->section = $section;

        $this->section?->addMeal($this);

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function isInvisible(): ?bool
    {
        return $this->invisible;
    }

    public function setInvisible(?bool $invisible): self
    {
        $this->invisible = $invisible;

        return $this;
    }
}
