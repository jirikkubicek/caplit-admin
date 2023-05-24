<?php

namespace App\Service;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class User extends CRMService implements CRMServiceInterface
{
    /**
     * @var string
     */
    private string $originPassword;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->setEntityClassName(Users::class);

        parent::__construct($entityManager, $logger);
    }

    /**
     * @param Users $entity
     * @return bool
     */
    public function addOrEdit(object $entity): bool
    {
        if ($entity->getPassword() === null) {
            $hashedPassword = $this->getOriginPassword();
        } else {
            $password = (string) $entity->getPassword();
            $hashedPassword = $this->userPasswordHasher->hashPassword($entity, $password);
        }

        $entity->setPassword($hashedPassword);

        return parent::addOrEdit($entity);
    }

    /**
     * @return string
     */
    public function getOriginPassword(): string
    {
        return $this->originPassword;
    }

    /**
     * @param string $originPassword
     * @return self
     */
    public function setOriginPassword(string $originPassword): self
    {
        $this->originPassword = $originPassword;

        return $this;
    }
}
