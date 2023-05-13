<?php

namespace App\Service;

use App\Entity\Settings as EntitySettings;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Settings extends BaseCRM implements CRMServiceInterface {
    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(EntitySettings::class);
    }
}