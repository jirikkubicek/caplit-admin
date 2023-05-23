<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course implements CRMEntityInterface
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
    private string $name;

    /**
     * @var Collection<int, Meal>
     */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Meal::class, orphanRemoval: false)]
    private Collection $meals;

    /**
     * @var boolean|null
     */
    #[ORM\Column(name: "is_default", nullable: true)]
    private ?bool $isDefault = null;

    /**
     * @param CourseRepository $repository
     */
    public function __construct(private CourseRepository $repository)
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
            $meal->setCourse($this);
        }

        return $this;
    }

    /**
     * @param Meal $meal
     * @return self
     */
    public function removeMeal(Meal $meal): self
    {
        if ($this->meals->removeElement($meal)) {
            if ($meal->getCourse() === $this) {
                $defaultCourse = $this->repository->findOneBy(["is_default" => 1]);

                if ($defaultCourse === null) {
                    throw new Exception("You have to set one default course");
                }

                $meal->setCourse($defaultCourse);
            }
        }

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isIsDefault(): ?bool
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
