<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;

class CRMService implements CRMServiceInterface
{
    /**
     * @var class-string<object>
     */
    private string $entityClassName;
    /**
     * @var FormInterface
     */
    private FormInterface $form;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param class-string<object> $entityClassName
     * @return self
     */
    public function setEntityClassName(string $entityClassName): self
    {
        if (class_exists($entityClassName)) {
            $this->entityClassName = $entityClassName;
        } else {
            throw new Exception(
                sprintf("Entity '%s' doesn't exists.", $entityClassName)
            );
        }

        return $this;
    }

    /**
     * @return class-string<object>
     */
    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    /**
     * @param FormInterface $form
     * @return self
     */
    public function setForm(FormInterface $form): self
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @return EntityRepository<object>
     */
    private function getEntityRepository(): EntityRepository
    {
        $repository = $this->entityManager->getRepository($this->getEntityClassName());

        if (!$repository instanceof EntityRepository) {
            throw new Exception(
                sprintf("For entity '%s' wasn't found repository.", $this->getEntityClassName())
            );
        }

        return $repository;
    }

    /**
     * Add or edit (if already exists) entity in database
     *
     * @param object $entity
     * @return bool
     */
    public function addOrEdit(object $entity): bool     // @todo Dle stavu zobrazí controller zprávu
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
            $this->logger->error(__METHOD__ . " - " . $exceptionMessage);

            return false;
        }

        return true;
    }

    /**
     * Remove entity from database
     *
     * @param object $entity
     * @return boolean
     */
    public function remove(object $entity): bool
    {
        try {
            $this->entityManager->remove($entity);      // @todo Dle stavu zobrazí controller zprávu
            $this->entityManager->flush();
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
            $this->logger->error(__METHOD__ . " - " . $exceptionMessage);

            return false;
        }

        return true;
    }

    /**
     * @return array<int,object>
     */
    public function findAll(): array
    {
        return $this->getEntityRepository()->findAll();
    }

    /**
     * @param string $orderBy
     * @param string $direction
     * @return array<object>
     */
    public function findAllOrdered(string $orderBy, string $direction): array
    {
        return $this->getEntityRepository()->findBy([], [$orderBy => $direction]);
    }

    /**
     * @param array<string,mixed> $params
     * @return object|null
     */
    public function findOneBy(array $params): ?object
    {
        return $this->getEntityRepository()->findOneBy($params);
    }

    /**
     * @param string $propertyName
     * @return boolean
     */
    public function entityHasProperty(string $propertyName): bool
    {
        $columns = $this->entityManager->getClassMetadata($this->getEntityClassName())->getColumnNames();
        foreach (
            $this->entityManager->getClassMetadata(
                $this->getEntityClassName()
            )->getAssociationMappings() as $mapping
        ) {
            $columns[] = $mapping["fieldName"];
        }

        return in_array($propertyName, $columns);
    }
}
