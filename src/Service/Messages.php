<?php

namespace App\Service;

class Messages implements MessagesInterface
{
    public const ACTION_KEY_NAME = "action";
    public const NAME_KEY_NAME = "name";
    public const TYPE_KEY_NAME = "type";
    public const MESSAGE_KEY_NAME = "message";

    /**
     * @var array
     */
    private array $messages = [];

    /**
     * @param string $name
     * @param string $message
     * @param string $action
     * @param string|null $type
     * @return self
     */
    public function add(string $name, string $message, string $action = "general", ?string $type = null): self
    {
        if ($type === null) {
            $this->messages[$action][$name] = $message;
        } else {
            $this->messages[$action][$name][$type] = $message;
        }

        return $this;
    }

    /**
     * @param array $messages
     * @return self
     */
    public function addMessages(array $messages): self
    {
        foreach ($messages as $message) {
            if (isset($message[self::ACTION_KEY_NAME])) {
                $this->add(
                    name: $message[self::NAME_KEY_NAME],
                    message: $message[self::MESSAGE_KEY_NAME],
                    action: $message[self::ACTION_KEY_NAME],
                    type: (isset($message[self::TYPE_KEY_NAME]) ? $message[self::TYPE_KEY_NAME] : null)
                );
            } else {
                $this->add(
                    name: $message[self::NAME_KEY_NAME],
                    message: $message[self::MESSAGE_KEY_NAME],
                    type: (isset($message[self::TYPE_KEY_NAME]) ? $message[self::TYPE_KEY_NAME] : null)
                );
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $action
     * @param string|null $type
     * @return string
     */
    public function get(string $name, string $action = "general", ?string $type = null): string
    {
        if ($type === null) {
            return (isset($this->messages[$action][$name])) ? $this->messages[$action][$name] : "";
        } else {
            return (isset($this->messages[$action][$name][$type])) ? $this->messages[$action][$name][$type] : "";
        }
    }
}
