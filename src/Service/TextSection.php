<?php

namespace App\Service;

use App\Entity\TextSection as EntityTextSection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TextSection extends BaseCRM implements CRMServiceInterface {
    public function __construct(EntityManagerInterface $EntityManager, LoggerInterface $Logger) {
        parent::__construct($EntityManager, $Logger);

        $this->setEntityClassName(EntityTextSection::class);
    }
}