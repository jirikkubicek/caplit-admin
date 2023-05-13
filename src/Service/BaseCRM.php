<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;

 abstract class BaseCRM {

    private string $entityClassName;
    protected FormInterface $Form;

    /**
     * @param Doctrine\ORM\EntityManagerInterface $EntityManager
     * @param Psr\Log\LoggerInterface $Logger
     */
    public function __construct(
        protected EntityManagerInterface $EntityManager,
        protected LoggerInterface $Logger
    ) { }

    public function setForm(FormInterface $Form): self {
        $this->Form = $Form;

        return $this;
    }

    public function getForm(): FormInterface {
        return $this->Form;
    }

    /**
     * Add or edit (if already exists) entity in database through EntityManagerInterface
     * 
     * @param Symfony\Bridge\Doctrine\Form\Type\EntityType $Entity
     * 
     * @return string|bool Returns exception's error message if fails or true if record was added or updated
     */
    public function addOrEdit(object $Entity): string|bool {
        try { 
            $this->EntityManager->persist($Entity);
            $this->EntityManager->flush();
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
            $this->Logger->error(__METHOD__ . " - " . $exceptionMessage);
            
            return $exceptionMessage;
        }

        return true;
    }

    /**
     * Remove entity from database through EntityManagerInterface
     * 
     * @param Symfony\Bridge\Doctrine\Form\Type\EntityType $Entity
     * 
     * @return string|bool Returns exception's error message if fails or true if record was removed
     */
    public function remove(object $Entity): string|bool {
        try {
            $this->EntityManager->remove($Entity);
            $this->EntityManager->flush();
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
            $this->Logger->error(__METHOD__ . " - " . $exceptionMessage);

            return $exceptionMessage;
        }

        return true;
    }

    /**
     * @param string $repositoryName
     * 
     * @return array Returns array of all Symfony\Bridge\Doctrine\Form\Type\EntityType from $repositoryName repository
     */
    protected function findAllRecords(): array {
        return $this->getActualRepository()->findAll();
    }

    /**
     * @param string $repositoryName
     * @param array $params
     * 
     * @return object|null Returns Symfony\Bridge\Doctrine\Form\Type\EntityType with data from database with appropriate data or null if $id is not integer or record was not found
     */
    public function findRecordBy(array $params): ?object {
        if(!empty($params) && is_array($params)) {
            return $this->getActualRepository()->findOneBy($params);
        } else {
            return null;
        }
    }

    protected function setEntityClassName(string $entityClassName): self {
        $this->entityClassName = $entityClassName;

        return $this;
    }

    protected function getEntityClassName(): string {
        return $this->entityClassName;
    }

    /**
     * @return array Returns array of all entities from database
     */
    public function findAll(): array {
        return $this->findAllRecords();
    }

    public function findAllOrdered(string $orderBy, string $direction): array {
        return $this->getActualRepository()->findBy([], [$orderBy => $direction]);
    }

    /**
     * @param int $id
     * 
     * @return object|null Returns entity with data from database with appropriate data or null if $id is not integer or record was not found
     */
    public function findById(int $id): ?object {
        return $this->findRecordBy(["id" => $id]);
    }

    private function getActualRepository(): EntityRepository {
        return $this->EntityManager->getRepository($this->getEntityClassName());
    }

    public function checkEntityHasProperty(string $propertyName): bool {
        $columns = $this->EntityManager->getClassMetadata($this->getEntityClassName())->getColumnNames();
        foreach($this->EntityManager->getClassMetadata($this->getEntityClassName())->getAssociationMappings() as $Mapping) {
            $columns[] = $Mapping["fieldName"];
        }

        return in_array($propertyName, $columns);
    }
}