<?php

namespace App\Service;

use App\Entity\TextSection as TextSectionEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class TextSection extends CRMService implements CRMServiceInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->setEntityClassName(TextSectionEntity::class);
    }

    /**
     * @param TextSectionEntity $entity
     * @return bool
     * @throws \Exception
     */
    public function remove(object $entity): bool
    {
        $defaultSections = $this->findAllBy(["isDefault" => 1]);
        if (in_array($entity, $defaultSections) && count($defaultSections) === 1) {
            $thisIsLastDefault = true;
        } else {
            $thisIsLastDefault = false;
        }

        $defaultSection = null;
        foreach ($defaultSections as $section) {
            if ($entity !== $section) {
                $defaultSection = $section;
            }
        }

        if (!$defaultSection instanceof TextSectionEntity || $thisIsLastDefault === true) {
            throw new \Exception("At least one default section of type 'App\Entity\TextSection' must be created.");
        }

        foreach ($entity->getTexts() as $text) {
            $entity->removeText($text);

            $text->setTextSection($defaultSection);
            $this->addOrEdit($text);
        }

        $removeChildrenResult = $entity->getTexts()->isEmpty();
        $removeResult = parent::remove($entity);

        return $removeChildrenResult && $removeResult;
    }
}
