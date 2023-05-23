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
        foreach ($entity->getMeals() as $meal) {
            $entity->removeMeal($meal);
        }

        $removeChildrenResult = $entity->getMeals()->isEmpty();
        $removeResult = parent::remove($entity);

        return $removeChildrenResult && $removeResult;
    }
}
