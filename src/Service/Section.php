<?php

namespace App\Service;

use App\Entity\Section as SectionEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Psr\Log\LoggerInterface;

class Section extends BaseCRM implements CRMServiceInterface {
    /**
     * Calls parent's __construct and sets used entity class name
     * 
     * @param Doctrine\ORM\EntityManagerInterface $EntityManager
     * @param Psr\Log\LoggerInterface $Logger
     */
    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger) {
        parent::__construct($EntityManager, $Logger);
        $this->setEntityClassName(SectionEntity::class);
    }

    private function getDefaultSection(): SectionEntity {
        return $this->findRecordBy(["is_default" => 1]);
    }

    private function setDefaultOnChilds(PersistentCollection $Meals): string|bool {
        $editResult = true;

        foreach($Meals as $Meal) {
            $Meal->setSection($this->getDefaultSection());

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