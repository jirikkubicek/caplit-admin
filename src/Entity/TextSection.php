<?php

namespace App\Entity;

use App\Repository\TextSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TextSectionRepository::class)]
class TextSection implements CRMEntityInterface
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
    #[Assert\Length(max: 255, maxMessage: "Maximální délka názvu je 255 znaků")]
    #[Assert\NotBlank(message: "Název nesmí být prázdný")]
    private string $name;

    /**
     * @var Collection<int, Text>
     */
    #[ORM\OneToMany(mappedBy: 'textSection', targetEntity: Text::class)]
    private Collection $texts;

    /**
     * @var boolean|null
     */
    #[ORM\Column(name: "is_default", nullable: true)]
    private ?bool $isDefault = null;

    /**
     * @param TextSectionRepository $repository
     */
    public function __construct(private TextSectionRepository $repository)
    {
        $this->texts = new ArrayCollection();
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
     * @return Collection<int, Text>
     */
    public function getTexts(): Collection
    {
        return $this->texts;
    }

    /**
     * @param Text $text
     * @return self
     */
    public function addText(Text $text): self
    {
        if (!$this->texts->contains($text)) {
            $this->texts->add($text);
            $text->setTextSection($this);
        }

        return $this;
    }

    /**
     * @param Text $text
     * @return self
     */
    public function removeText(Text $text): self
    {
        if ($this->texts->removeElement($text)) {
            if ($text->getTextSection() === $this) {
                $defaultSection = $this->repository->findOneBy(["is_default" => 1]);

                if ($defaultSection === null) {
                    throw new Exception("You have to set one default section");
                }

                $text->setTextSection($defaultSection);
            }
        }

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
