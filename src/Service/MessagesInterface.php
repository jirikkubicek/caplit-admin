<?php

namespace App\Service;

interface MessagesInterface
{
    /**
     * @param string $name
     * @param string $message
     * @param string $action
     * @param string|null $type
     * @return self
     */
    public function add(string $name, string $message, string $action = "", ?string $type = null): self;

    /**
     * @param array $messages
     * @return self
     */
    public function addMessages(array $messages): self;

    /**
     * @param string $name
     * @param string $action
     * @param string|null $type
     * @return string
     */
    public function get(string $name, string $action = "", ?string $type = null): string;
}
