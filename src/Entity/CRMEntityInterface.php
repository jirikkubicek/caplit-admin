<?php

namespace App\Entity;

interface CRMEntityInterface
{
    /**
     * @return integer|null
     */
    public function getId(): ?int;
    /**
     * @return self
     */
    public function resetId(): self;
}
