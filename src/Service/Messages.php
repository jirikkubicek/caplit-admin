<?php

namespace App\Service;

use Exception;

final class Messages implements MessagesInterface
{
    /**
     * @var array<string,string|array<string,string>>
     */
    private array $messages = [];

    /**
     * @param string $name
     * @param string $message
     * @param string $type
     * @return self
     */
    public function add(string $name, string $message, string $type = ""): self
    {
        $this->messages[$name] = ["message" => $message, "type" => $type];

        return $this;
    }

    /**
     * @param array<string,string|array<string,string>> $messages
     * @return self
     * @throws Exception
     */
    public function addMessages(array $messages): self
    {
        foreach ($messages as $name => $messageContent) {
            $messageType = "";
            $message = "";
            if (isset($messageContent["type"])) {
                $messageType = $messageContent["type"];
            }

            if (isset($messageContent["message"])) {
                $message = $messageContent["message"];
            } elseif (is_string($messageContent)) {
                $message = $messageContent;
            } else {
                throw new Exception("Variable \$messages must have name of message in key and message content in value or must contains array with format \"type\" = (string) \$messageType and \"message\" = (string) \$messageContent");
            }

            $this->add($name, $message, $messageType);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    public function get(string $name): string
    {
        if (isset($this->messages[$name]["message"])) {
            return $this->messages[$name]["message"];
        } else {
            throw new Exception(
                sprintf(
                    "Message key '%s' doesn't exists. Did you mean one of these %s?",
                    $name,
                    $this->guessMessageName($name)
                )
            );
        }
    }

    /**
     * @param string $name
     * @return string
     */
    private function guessMessageName(string $name): string
    {
        $options = [];
        foreach ($this->messages as $existingName => $messageContent) {
            if (levenshtein($name, $existingName) <= 5) {
                $options[] = "'" . $existingName . "'";
            }
        }

        return implode(", ", $options);
    }
}
