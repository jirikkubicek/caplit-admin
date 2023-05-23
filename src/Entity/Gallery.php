<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
class Gallery implements CRMEntityInterface
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
    private ?string $title = null;

    /**
     * @var string
     */
    #[ORM\Column(length: 255)]
    private string $filename;

    /**
     * @var integer|null
     */
    #[ORM\Column(name: "photo_order", nullable: true)]
    private ?int $photoOrder = null;

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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return self
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getPhotoOrder(): ?int
    {
        return $this->photoOrder;
    }

    /**
     * @param integer|null $photoOrder
     * @return self
     */
    public function setPhotoOrder(?int $photoOrder): self
    {
        $this->photoOrder = $photoOrder;

        return $this;
    }
}
