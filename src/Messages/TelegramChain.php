<?php

namespace TelegramNotifications\Messages;

use Illuminate\Support\Str;

class TelegramChain extends TelegramEntity
{
    protected $messages = [];

    public static function __callStatic($method, $args)
    {
        return (new static())
            ->{$method}(...$args);
    }

    public function __call($method, $args)
    {
        $messageClass = __NAMESPACE__.'\Telegram'.Str::studly($method);

        if (class_exists($messageClass)) {
            $this->messages[] = new $messageClass(...$args);
        }

        return $this;
    }

    public function messages()
    {
        return $this->messages;
    }
}