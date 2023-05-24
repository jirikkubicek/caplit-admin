<?php

namespace App\Service;

use App\Entity\Section as SectionEntity;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class Section extends CRMService implements CRMServiceInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->setEntityClassName(SectionEntity::class);
    }

    /**
     * @param SectionEntity $entity
     * @return boolean
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

        if (!$defaultSection instanceof SectionEntity || $thisIsLastDefault === true) {
            throw new \Exception("At least one default section of type 'App\Entity\Section' must be created.");
        }

        foreach ($entity->getMeals() as $meal) {
            $entity->removeMeal($meal);

            $meal->setSection($defaultSection);
            $this->addOrEdit($meal);
        }

        $removeChildrenResult = $entity->getMeals()->isEmpty();
        $removeResult = parent::remove($entity);

        return $removeChildrenResult && $removeResult;
    }
}
