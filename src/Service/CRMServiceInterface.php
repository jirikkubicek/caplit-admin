<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

interface CRMServiceInterface {
    public function setForm(FormInterface $Form);
    public function getForm(): FormInterface;

    public function findById(int $id): ?object;
    public function findAll(): array;
    public function findAllOrdered(string $orderBy, string $direction): array;

    public function checkEntityHasProperty(string $orderBy): bool;

    public function addOrEdit(object $Entity): string|bool;
    public function remove(object $Entity): string|bool;
}