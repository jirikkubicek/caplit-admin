<?php

namespace App\Service;

use GdImage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Image
{
    /**
     * @var array<int,string>
     */
    private array $supportedMimeTypes = [
        "image/jpeg",
        "image/png"
    ];

    /**
     * @param UploadedFile $file
     * @param string $directoryPath
     * @param string|null $fileName
     * @return boolean
     */
    public function upload(UploadedFile $file, string $directoryPath, ?string $fileName = null): bool
    {
        if ($this->isFormatSupported((string)$file->getMimeType())) {
            $fileName = ($fileName === null ? $file->getClientOriginalName() : $fileName);
            $file->move($directoryPath, $fileName); // @todo: To try / catch
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
     * @param string $suffix
     * @return boolean
     * @throws \Exception
     */
    public function createThumbnail(
        string $originalFileName,
        string $directoryPath,
        string $sourcePath,
        int $maxWidth,
        int $maxHeight,
        string $suffix = "_thumb"
    ): bool {
        $sourcePathWithFileName = $sourcePath . "/" . $originalFileName;
        if (
            file_exists($sourcePathWithFileName)
            && $this->isFormatSupported(mime_content_type($sourcePathWithFileName))
        ) {
            $extension = $this->getFileNameExtension($originalFileName);
            $thumbnailName = $this->getThumbnailName($originalFileName, $suffix);

            $destinationPathWithFileName = $directoryPath . "/" . $thumbnailName;
            list($sourceImageWidth, $sourceImageHeight) = (
                is_array(getimagesize($sourcePathWithFileName)) ?
                    getimagesize($sourcePathWithFileName) :
                    []
            );

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

            if ($sourceImage instanceof GdImage && $thumbnail instanceof GdImage) {
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
            throw new \Exception("Nepodporovaný formát");
        }
    }

    /**
     * @param string $filename
     * @param GdImage $image
     * @return void
     */
    private function fixImageOrientation(string $filename, GdImage &$image): void
    {
        $exifData = @exif_read_data($filename);

        if (isset($exifData['Orientation'])) {
            $exifOrientation = $exifData['Orientation'];
            switch ($exifOrientation) {
                case 1: // nothing
                    break;

                case 2: // horizontal flip
                    imageflip($image, 1);
                    break;

                case 3: // 180 rotate left
                    $image = imagerotate($image, 180, 0);
                    break;

                case 4: // vertical flip
                    imageflip($image, 2);
                    break;

                case 5: // vertical flip + 90 rotate right
                    imageflip($image, 2);
                    $image = imagerotate($image, -90, 0);
                    break;

                case 6: // 90 rotate right
                    $image = imagerotate($image, -90, 0);
                    break;

                case 7: // horizontal flip + 90 rotate right
                    imageflip($image, 1);
                    $image = imagerotate($image, -90, 0);
                    break;

                case 8:    // 90 rotate left
                    $image = imagerotate($image, 90, 0);
                    break;
            }
        }
    }

    /**
     * @param string $fileName
     * @param string $suffix
     * @return string
     */
    public function getThumbnailName(string $fileName, string $suffix): string
    {
        return $this->getFileNameBase($fileName) . $suffix . "." . $this->getFileNameExtension($fileName);
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

        if (!empty($parts)) {
            return array_pop($parts);
        } else {
            return "";
        }
    }

    /**
     * @param string $fileName
     * @return array<int,string>
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
        return match (strtolower($extension)) {
            "jpg", "jpeg" => imagecreatefromjpeg($sourcePathWithFileName),
            "png" => imagecreatefrompng($sourcePathWithFileName),
            default => false,
        };
    }

    /**
     * @param GdImage $thumbnail
     * @param string $destinationPathWithFileName
     * @param string $extension
     * @return boolean
     */
    private function exportThumbnail(GdImage $thumbnail, string $destinationPathWithFileName, string $extension): bool
    {
        return match (strtolower($extension)) {
            "jpg", "jpeg" => imagejpeg($thumbnail, $destinationPathWithFileName),
            "png" => imagepng($thumbnail, $destinationPathWithFileName),
            default => false,
        };
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
     * @param string|false $mimeType
     * @return bool
     */
    public function isFormatSupported(string|false $mimeType): bool
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

        return $this->getFileNameBase($fileName) . $uid . "." . $this->getFileNameExtension($fileName);
    }
}
