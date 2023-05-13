<?php

namespace App\Service;

use App\Entity\Gallery as EntityGallery;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Gallery extends BaseCRM implements CRMServiceInterface {
    const THUMBNAIL_SUFIX = "";

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

    private function getGalleryDirectory(): string {
        return $this->galleryDirectory;
    }

    private function getThumbnailDirectory(): string {
        return $this->thumbnailDirectory;
    }

    public function addOrEdit(object $Entity): string|bool {
        $File = $this->getForm()->get("filename")->getData();
        $newImageFilename = $this->ImageService->generateUniqueFileName($File->getClientOriginalName());
        $oldImageFilename = $Entity->getFilename();

        $uploaded = $this->ImageService->upload($File, $this->getGalleryDirectory(), $newImageFilename); // 3. parameter - upravený název s UID
        $thumbCreated = $this->ImageService->createThumbnail($newImageFilename, $this->getThumbnailDirectory(), $this->getGalleryDirectory(), 220, 500, self::THUMBNAIL_SUFIX);

        if($uploaded && $thumbCreated) {
            if($oldImageFilename !== null) {
                $this->ImageService->delete($oldImageFilename, $this->getGalleryDirectory());
                $this->ImageService->delete($this->ImageService->getThumbnailName($oldImageFilename, self::THUMBNAIL_SUFIX), $this->getThumbnailDirectory());
            } 

            $Entity->setFilename($newImageFilename);
        } else {
            $this->ImageService->delete($newImageFilename, $this->getGalleryDirectory());
            $this->ImageService->delete($this->ImageService->getThumbnailName($newImageFilename, self::THUMBNAIL_SUFIX), $this->getThumbnailDirectory());

            return false;
        }
        
        return parent::addOrEdit($Entity);
    }

    public function remove(object $Entity): string|bool {
        $this->ImageService->delete($Entity->getFilename(), $this->getGalleryDirectory());
        $this->ImageService->delete($this->ImageService->getThumbnailName($Entity->getFilename(), self::THUMBNAIL_SUFIX), $this->getThumbnailDirectory());

        return parent::remove($Entity);
    }
}