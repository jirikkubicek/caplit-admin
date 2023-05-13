<?php 

namespace App\Service;

use App\Entity\Meal as EntityMeal;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Meal extends BaseCRM implements CRMServiceInterface {
    private array $actualMenu = [];

    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger, private Section $Section, private Course $Course) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(EntityMeal::class);

        $this->buildActualMenu();
    }

    private function buildActualMenu(): void {
        foreach($this->findAll() as $Meal) {
            $this->actualMenu[$Meal->getSection()->getName()]["show_courses"] = $Meal->getSection()->isShowCourses();
            $this->actualMenu[$Meal->getSection()->getName()]["courses"][$Meal->getCourse()->getName()][] = $Meal;
        }
    }

    public function getActualMenu(): array {
        return $this->actualMenu;
    }
}