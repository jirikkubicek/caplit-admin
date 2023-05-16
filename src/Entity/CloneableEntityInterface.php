<?php

namespace App\Entity;

interface CloneableEntityInterface
{
    public function resetId(): self;
}
