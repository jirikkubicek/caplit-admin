<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

interface CRMServiceInterface
{
    /**
     * @param FormInterface $form
     * @return void
     */
    public function setForm(FormInterface $form);

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface;

    /**
     * @param class-string<object> $entityClassName
     * @return self
     */
    public function setEntityClassName(string $entityClassName): self;

    /**
     * @return class-string<object>
     */
    public function getEntityClassName(): string;

    /**
     * @param array<string,mixed> $params
     * @return object|null
     */
    public function findOneBy(array $params): ?object;

    /**
     * @return array<int,object>
     */
    public function findAll(): array;

    /**
     * @param string $orderBy
     * @param string $direction
     * @return array<int,object>
     */
    public function findAllOrdered(string $orderBy, string $direction): array;

    /**
     * @param string $orderBy
     * @return boolean
     */
    public function entityHasProperty(string $orderBy): bool;

    /**
     * @param object $entity
     * @return boolean
     */
    public function addOrEdit(object $entity): bool;

    /**
     * @param object $entity
     * @return boolean
     */
    public function remove(object $entity): bool;
}
