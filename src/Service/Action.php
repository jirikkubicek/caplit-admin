<?php

namespace App\Service;

use App\Entity\Actions;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Action extends BaseCRM implements CRMServiceInterface {
    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(Actions::class);
    }
}