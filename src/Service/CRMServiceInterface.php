<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

interface CRMServiceInterface
{
    /**
     * @param FormInterface $Form
     * @return void
     */
    public function setForm(FormInterface $Form);

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface;

    /**
     * @param integer $id
     * @return object|null
     */
    public function findById(int $id): ?object;

    /**
     * @return array
     */
    public function findAll(): array;

    /**
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public function findAllOrdered(string $orderBy, string $direction): array;

    /**
     * @param string $orderBy
     * @return boolean
     */
    public function checkEntityHasProperty(string $orderBy): bool;

    /**
     * @param object $Entity
     * @return string|boolean
     */
    public function addOrEdit(object $Entity): string|bool;

    /**
     * @param object $Entity
     * @return string|boolean
     */
    public function remove(object $Entity): string|bool;
}
