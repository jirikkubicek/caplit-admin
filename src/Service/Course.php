<?php

namespace App\Service;

use App\Entity\Course as EntityCourse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Psr\Log\LoggerInterface;

class Course extends BaseCRM implements CRMServiceInterface {
    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(EntityCourse::class);
    }

    private function getDefaultSection(): EntityCourse {
        return $this->findRecordBy(["is_default" => 1]);
    }

    private function setDefaultOnChilds(PersistentCollection $Meals): string|bool {
        $editResult = true;

        foreach($Meals as $Meal) {
            $Meal->setCourse($this->getDefaultSection());

            if($editResult === true) {
                $editResult = $this->addOrEdit($Meal);
            }
        }

        return $editResult;
    }

    public function remove(object $Entity): string|bool
    {
        $changeToDefault = $this->setDefaultOnChilds($Entity->getMeals());
        $removeResult = parent::remove($Entity);

        if($removeResult !== true) {
            return $removeResult;
        } 

        if($changeToDefault !== true) {
            return $changeToDefault;
        }

        return true;
    }
}