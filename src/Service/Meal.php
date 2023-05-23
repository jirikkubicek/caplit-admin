<?php

namespace App\Service;

use App\Entity\Meal as MealEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class Meal extends CRMService implements CRMServiceInterface
{
    /**
     * @var array<string,array<string,bool|null|array<string,MealEntity>>>
     */
    private array $actualMenu = [];

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->setEntityClassName(MealEntity::class);
    }

    /**
     * @return void
     */
    public function buildActualMenu(): void
    {
        /**
         * @var MealEntity $meal
         */
        foreach ($this->findAll() as $meal) {
            $this->actualMenu[$meal->getSection()->getName()]["show_courses"] = $meal->getSection()->isShowCourses();
            $this->actualMenu[$meal->getSection()->getName()]["courses"][$meal->getCourse()->getName()][] = $meal;
        }
    }

    /**
     * @return array<string,array<string,bool|null|array<string,MealEntity>>>
     */
    public function getActualMenu(): array
    {
        return $this->actualMenu;
    }
}
