<?php

namespace App\Entity;

use App\Repository\TextSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TextSectionRepository::class)]
class TextSection implements CloneableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: "Maximální délka názvu je 255 znaků")]
    #[Assert\NotBlank(message: "Název nesmí být prázdný")]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'text_section', targetEntity: Text::class)]
    private Collection $texts;

    public function __construct()
    {
        $this->texts = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Text>
     */
    public function getTexts(): Collection
    {
        return $this->texts;
    }

    public function addText(Text $text): self
    {
        if (!$this->texts->contains($text)) {
            $this->texts->add($text);
            $text->setTextSection($this);
        }

        return $this;
    }

    public function removeText(Text $text): self
    {
        if ($this->texts->removeElement($text)) {
            // set the owning side to null (unless already changed)
            if ($text->getTextSection() === $this) {
                $text->setTextSection(null);
            }
        }

        return $this;
    }
}
