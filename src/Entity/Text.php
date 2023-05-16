<?php

namespace App\Entity;

use App\Repository\TextRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TextRepository::class)]
class Text implements CloneableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: "Maximální délka nadpisu je 255 znaků")]
    private ?string $header = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Text nesmí být prázdný")]
    private ?string $text = null;

    #[ORM\ManyToOne(inversedBy: 'texts')]
    private ?TextSection $textSection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function resetId(): self
    {
        $this->id = null;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(?string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTextSection(): ?TextSection
    {
        return $this->textSection;
    }

    public function setTextSection(?TextSection $textSection): self
    {
        $this->textSection = $textSection;

        return $this;
    }
}
