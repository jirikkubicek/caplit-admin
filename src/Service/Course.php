<?php

namespace App\Service;

use App\Entity\Course as CourseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class Course extends CRMService implements CRMServiceInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->setEntityClassName(CourseEntity::class);
    }

    /**
     * @param CourseEntity $entity
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
