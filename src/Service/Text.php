<?php

namespace App\Service;

use App\Entity\Text as EntityText;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Text extends BaseCRM implements CRMServiceInterface {
    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(EntityText::class);
    }
}