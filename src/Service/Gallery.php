<?php

namespace App\Service;

use App\Entity\Gallery as EntityGallery;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Gallery extends BaseCRM implements CRMServiceInterface
{
    private const THUMBNAIL_SUFIX = "";

    /**
     * @param string $galleryDirectory
     * @param string $thumbnailDirectory
     * @param Image $ImageService
     * @param EntityManagerInterface $EntityManager
     * @param LoggerInterface $Logger
     */
    public function __construct(
        private string $galleryDirectory,
        private string $thumbnailDirectory,
        private Image $ImageService,
        EntityManagerInterface $EntityManager,
        LoggerInterface $Logger
    ) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(EntityGallery::class);
    }

    /**
     * @return string
     */
    private function getGalleryDirectory(): string
    {
        return $this->galleryDirectory;
    }

    /**
     * @return string
     */
    private function getThumbnailDirectory(): string
    {
        return $this->thumbnailDirectory;
    }

    /**
     * @param object $Entity
     * @return string|boolean
     */
    public function addOrEdit(object $Entity): string|bool
    {
        $File = $this->getForm()->get("filename")->getData();
        $newImageFilename = $this->ImageService->generateUniqueFileName($File->getClientOriginalName());
        $oldImageFilename = $Entity->getFilename();

        $uploaded = $this->ImageService->upload(
            $File,
            $this->getGalleryDirectory(),
            $newImageFilename // upravený název s UID
        );
        $thumbCreated = $this->ImageService->createThumbnail(
            $newImageFilename,
            $this->getThumbnailDirectory(),
            $this->getGalleryDirectory(),
            220,
            500,
            self::THUMBNAIL_SUFIX
        );

        if ($uploaded && $thumbCreated) {
            if ($oldImageFilename !== null) {
                $this->ImageService->delete($oldImageFilename, $this->getGalleryDirectory());
                $this->ImageService->delete($this->ImageService->getThumbnailName(
                    $oldImageFilename,
                    self::THUMBNAIL_SUFIX
                ), $this->getThumbnailDirectory());
            }

            $Entity->setFilename($newImageFilename);
        } else {
            $this->ImageService->delete($newImageFilename, $this->getGalleryDirectory());
            $this->ImageService->delete($this->ImageService->getThumbnailName(
                $newImageFilename,
                self::THUMBNAIL_SUFIX
            ), $this->getThumbnailDirectory());

            return false;
        }

        return parent::addOrEdit($Entity);
    }

    /**
     * @param object $Entity
     * @return string|boolean
     */
    public function remove(object $Entity): string|bool
    {
        $this->ImageService->delete($Entity->getFilename(), $this->getGalleryDirectory());
        $this->ImageService->delete($this->ImageService->getThumbnailName(
            $Entity->getFilename(),
            self::THUMBNAIL_SUFIX
        ), $this->getThumbnailDirectory());

        return parent::remove($Entity);
    }
}
