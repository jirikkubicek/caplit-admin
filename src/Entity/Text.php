<?php

namespace App\Entity;

use App\Repository\TextRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TextRepository::class)]
class Text implements CRMEntityInterface
{
    /**
     * @var integer|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: "Maximální délka nadpisu je 255 znaků")]
    private ?string $header = null;

    /**
     * @var string
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Text nesmí být prázdný")]
    private string $text;

    /**
     * @var TextSection
     */
    #[ORM\ManyToOne(inversedBy: 'texts')]
    private TextSection $textSection;

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
     * @return string|null
     */
    public function getHeader(): ?string
    {
        return $this->header;
    }

    /**
     * @param string|null $header
     * @return self
     */
    public function setHeader(?string $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return TextSection
     */
    public function getTextSection(): TextSection
    {
        return $this->textSection;
    }

    /**
     * @param TextSection $textSection
     * @return self
     */
    public function setTextSection(TextSection $textSection): self
    {
        $this->textSection->removeText($this);

        $this->textSection = $textSection;

        $this->textSection->addText($this);

        return $this;
    }
}
