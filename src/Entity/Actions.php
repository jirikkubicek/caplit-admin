<?php

namespace App\Entity;

use App\Repository\ActionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActionsRepository::class)]
class Actions implements CRMEntityInterface
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
    #[Assert\NotBlank(message: "Nadpis nesmí být prázdný")]
    private string $header;

    /**
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(name: "date_from", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "Platné od musí být ve formátu DD.MM.RRRR HH:MM")]
    private ?\DateTimeInterface $dateFrom = null;

    /**
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(name: "date_to", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "Platné od musí být ve formátu DD.MM.RRRR HH:MM")]
    private ?\DateTimeInterface $dateTo = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

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
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     * @return self
     */
    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTimeInterface|null $dateFrom
     * @return self
     */
    public function setDateFrom(?\DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    /**
     * @param \DateTimeInterface|null $dateTo
     * @return self
     */
    public function setDateTo(?\DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return self
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
