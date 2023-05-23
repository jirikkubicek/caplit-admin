<?php

namespace App\Service;

use App\Entity\Section as SectionEntity;
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
     */
    public function remove(object $entity): bool
    {
        $defaultSection = $this->findOneBy(["isDefault" => 1]);
        if (!$defaultSection instanceof SectionEntity) {
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
