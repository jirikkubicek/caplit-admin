<?php

namespace App\Service;

use GdImage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image
{
    /**
     * @var array
     */
    private array $supportedMimeTypes = [
        "image/jpeg",
        "image/png"
    ];

    /**
     * @param UploadedFile $File
     * @param string $directoryPath
     * @param string|null $fileName
     * @return boolean
     */
    public function upload(UploadedFile $File, string $directoryPath, ?string $fileName = null): bool
    {
        if ($File && $this->isFormatSupported($File->getMimeType())) {
            $fileName = ($fileName === null ? $File->getClientOriginalName() : $fileName);
            $File->move($directoryPath, $fileName); // To try / catch
            if (file_exists($directoryPath . "/" . $fileName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $originalFileName
     * @param string $directoryPath
     * @param string $sourcePath
     * @param integer $maxWidth
     * @param integer $maxHeight
     * @param string $sufix
     * @return boolean
     */
    public function createThumbnail(
        string $originalFileName,
        string $directoryPath,
        string $sourcePath,
        int $maxWidth,
        int $maxHeight,
        string $sufix = "_thumb"
    ): bool {
        $sourcePathWithFileName = $sourcePath . "/" . $originalFileName;
        if (
            file_exists($sourcePathWithFileName)
            && $this->isFormatSupported(mime_content_type($sourcePathWithFileName))
        ) {
            $extension = $this->getFileNameExtension($originalFileName);
            $thumbnailName = $this->getThumbnailName($originalFileName, $sufix);

            $destinationPathWithFileName = $directoryPath . "/" . $thumbnailName;
            list($sourceImageWidth, $sourceImageHeight) = getimagesize($sourcePathWithFileName);

            $width = $sourceImageWidth;
            $height = $sourceImageHeight;
            if ($height > $maxHeight) {
                $width = ($maxHeight / $height) * $width;
                $height = $maxHeight;
            }

            if ($width > $maxWidth) {
                $height = ($maxWidth / $width) * $height;
                $width = $maxWidth;
            }

            $thumbnail = imagecreatetruecolor($width, $height);
            $sourceImage = $this->getSourceImage($sourcePathWithFileName, $extension);

            if ($sourceImage instanceof GdImage) {
                $thumbnailCreateResult = imagecopyresampled(
                    $thumbnail,
                    $sourceImage,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $sourceImageWidth,
                    $sourceImageHeight
                );
                $this->fixImageOrientation($sourcePathWithFileName, $thumbnail);

                if ($thumbnailCreateResult === true) {
                    if ($this->exportThumbnail($thumbnail, $destinationPathWithFileName, $extension)) {
                        return true;
                    } else {
                        throw new \Exception("Chyba při ukládání náhledu");
                    }
                } else {
                    throw new \Exception("Chyba při vytváření náhledu");
                }
            } else {
                throw new \Exception("Chyba při načítání zdrojového souboru");
            }
        } else {
            //throw new \Exception("Nepodporovaný formát");
        }

        return false;
    }

    /**
     * @param string $filename
     * @param GdImage $Image
     * @return void
     */
    private function fixImageOrientation(string $filename, GdImage &$Image): void
    {
        $exifData = @exif_read_data($filename);

        if (isset($exifData['Orientation'])) {
            $exifOrientation = $exifData['Orientation'];
            switch ($exifOrientation) {
                case 1: // nothing
                    break;

                case 2: // horizontal flip
                    imageflip($Image, 1);
                    break;

                case 3: // 180 rotate left
                    $Image = imagerotate($Image, 180, 0);
                    break;

                case 4: // vertical flip
                    imageflip($Image, 2);
                    break;

                case 5: // vertical flip + 90 rotate right
                    imageflip($Image, 2);
                    $Image = imagerotate($Image, -90, 0);
                    break;

                case 6: // 90 rotate right
                    $Image = imagerotate($Image, -90, 0);
                    break;

                case 7: // horizontal flip + 90 rotate right
                    imageflip($Image, 1);
                    $Image = imagerotate($Image, -90, 0);
                    break;

                case 8:    // 90 rotate left
                    $Image = imagerotate($Image, 90, 0);
                    break;
            }
        }
    }

    /**
     * @param string $fileName
     * @param string $sufix
     * @return string
     */
    public function getThumbnailName(string $fileName, string $sufix): string
    {
        return $this->getFileNameBase($fileName) . $sufix . "." . $this->getFileNameExtension($fileName);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getFileNameBase(string $fileName): string
    {
        $parts = $this->getFileNameParts($fileName);
        array_pop($parts);
        return implode(".", $parts);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getFileNameExtension(string $fileName): string
    {
        $parts = $this->getFileNameParts($fileName);
        return array_pop($parts);
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function getFileNameParts(string $fileName): array
    {
        return explode(".", $fileName);
    }

    /**
     * @param string $sourcePathWithFileName
     * @param string $extension
     * @return GdImage|false
     */
    private function getSourceImage(string $sourcePathWithFileName, string $extension): GdImage|false
    {
        switch (strtolower($extension)) {
            case "jpg":
            case "jpeg":
                return imagecreatefromjpeg($sourcePathWithFileName);
            case "png":
                return imagecreatefrompng($sourcePathWithFileName);
            default:
                return false;
        }
    }

    /**
     * @param GdImage $thumbnail
     * @param string $destinationPathWithFileName
     * @param string $extension
     * @return boolean
     */
    private function exportThumbnail(GdImage $thumbnail, string $destinationPathWithFileName, string $extension): bool
    {
        switch (strtolower($extension)) {
            case "jpg":
            case "jpeg":
                return imagejpeg($thumbnail, $destinationPathWithFileName);
            case "png":
                return imagepng($thumbnail, $destinationPathWithFileName);
            default:
                return false;
        }
    }

    /**
     * @param string|null $filename
     * @param string $directoryPath
     * @return void
     */
    public function delete(?string $filename, string $directoryPath): void
    {
        if ($filename !== null) {
            $Filesystem = new Filesystem();
            $Filesystem->remove($directoryPath . "/" . $filename);
        }
    }

    /**
     * @param string $mimeType
     * @return boolean
     */
    public function isFormatSupported(string $mimeType): bool
    {
        return in_array($mimeType, $this->supportedMimeTypes);
    }

    /**
     * @param string $fileName
     * @param integer $length
     * @return string
     */
    public function generateUniqueFileName(string $fileName, int $length = 6): string
    {
        $uid = "";

        for ($i = 0; $i < $length; $i++) {
            $uid .= mt_rand(0, 9);
        }

        $uniqueFileName = $this->getFileNameBase($fileName) . $uid . "." . $this->getFileNameExtension($fileName);

        return $uniqueFileName;
    }
}
