<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
class Gallery implements CloneableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(name: "photo_order", nullable: true)]
    private ?int $photoOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function resetId(): self
    {
        $this->id = null;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPhotoOrder(): ?int
    {
        return $this->photoOrder;
    }

    public function setPhotoOrder(?int $photoOrder): self
    {
        $this->photoOrder = $photoOrder;

        return $this;
    }
}
