<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section implements CRMEntityInterface
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
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Název je povinné pole")]
    #[Assert\Length(max: 100, maxMessage: "Maximální délka názvu je 100 znaků")]
    private string $name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var boolean|null
     */
    #[ORM\Column(name: "show_courses")]
    private ?bool $showCourses = null;

    /**
     * @var Collection<int, Meal>
     */
    #[ORM\OneToMany(mappedBy: 'section', targetEntity: Meal::class, orphanRemoval: false)]
    private Collection $meals;

    /**
     * @var boolean|null
     */
    #[ORM\Column(name: "is_default", nullable: true)]
    private ?bool $isDefault = null;

    /**
     *
     */
    public function __construct()
    {
        $this->meals = new ArrayCollection();
    }

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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isShowCourses(): ?bool
    {
        return $this->showCourses;
    }

    /**
     * @param boolean $showCourses
     * @return self
     */
    public function setShowCourses(bool $showCourses): self
    {
        $this->showCourses = $showCourses;

        return $this;
    }

    /**
     * @return Collection<int, Meal>
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    /**
     * @param Meal $meal
     * @return self
     */
    public function addMeal(Meal $meal): self
    {
        if (!$this->meals->contains($meal)) {
            $this->meals->add($meal);
            $meal->setSection($this);
        }

        return $this;
    }

    /**
     * @param Meal $meal
     * @return self
     */
    public function removeMeal(Meal $meal): self
    {
        $this->meals->removeElement($meal);

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * @param boolean|null $isDefault
     * @return self
     */
    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}
