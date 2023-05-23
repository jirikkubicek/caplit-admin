<?php

namespace App\Service;

interface MessagesInterface
{
    /**
     * @param string $name
     * @param string $message
     * @param string $type
     * @return self
     */
    public function add(string $name, string $message, string $type = ""): self;

    /**
     * @param array<string,string|array<string,string>> $messages
     * @return self
     */
    public function addMessages(array $messages): self;

    /**
     * @param string $name
     * @return string
     */
    public function get(string $name): string;
}
