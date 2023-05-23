<?php

namespace App\Service;

use App\Entity\Gallery as GalleryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Gallery extends CRMService implements CRMServiceInterface
{
    private const THUMBNAIL_SUFIX = "";

    /**
     * @param string $galleryDirectory
     * @param string $thumbnailDirectory
     * @param Image $imageService
     */
    public function __construct(
        private string $galleryDirectory,
        private string $thumbnailDirectory,
        private Image $imageService,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager, $logger);

        $this->setEntityClassName(GalleryEntity::class);
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
     * @param GalleryEntity $entity
     * @return boolean
     */
    public function addOrEdit(object $entity): bool
    {
        $file = $this->getForm()->get("filename")->getData();

        if (!$file instanceof UploadedFile) {
            throw new Exception("\$file has to be type of Symfony\Component\HttpFoundation\File\UploadedFile");
        }

        $newImageFilename = $this->imageService->generateUniqueFileName($file->getClientOriginalName());
        $oldImageFilename = $entity->getFilename();

        $uploaded = $this->imageService->upload(
            $file,
            $this->getGalleryDirectory(),
            $newImageFilename
        );
        $thumbCreated = $this->imageService->createThumbnail(
            $newImageFilename,
            $this->getThumbnailDirectory(),
            $this->getGalleryDirectory(),
            220,
            500,
            self::THUMBNAIL_SUFIX
        );

        if ($uploaded && $thumbCreated) {
            if ($oldImageFilename !== null) {
                $this->imageService->delete($oldImageFilename, $this->getGalleryDirectory());
                $this->imageService->delete($this->imageService->getThumbnailName(
                    $oldImageFilename,
                    self::THUMBNAIL_SUFIX
                ), $this->getThumbnailDirectory());
            }

            $entity->setFilename($newImageFilename);
        } else {
            $this->imageService->delete($newImageFilename, $this->getGalleryDirectory());
            $this->imageService->delete($this->imageService->getThumbnailName(
                $newImageFilename,
                self::THUMBNAIL_SUFIX
            ), $this->getThumbnailDirectory());

            return false;
        }

        return parent::addOrEdit($entity);
    }

    /**
     * @param GalleryEntity $entity
     * @return boolean
     */
    public function remove(object $entity): bool
    {
        $this->imageService->delete($entity->getFilename(), $this->getGalleryDirectory());
        $this->imageService->delete($this->imageService->getThumbnailName(
            $entity->getFilename(),
            self::THUMBNAIL_SUFIX
        ), $this->getThumbnailDirectory());

        return parent::remove($entity);
    }
}
