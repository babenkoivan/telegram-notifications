<?php

namespace TelegramNotifications\Messages;

use Illuminate\Support\Str;

class TelegramCollection
{
    protected $messages = [];

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    public function put(TelegramEntity $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    public function validate()
    {
        $this->map(function(TelegramEntity $message) {
            $message->validate();
        });

        return $this;
    }

    public function map($callback)
    {
        foreach ($this->messages as $message) {
            $callback($message);
        }

        return $this;
    }

    public function __call($method, $args)
    {
        $messageClass = __NAMESPACE__.'\Telegram'.Str::studly($method);

        if (class_exists($messageClass)) {
            $this->put(new $messageClass(...$args));
        }

        return $this;
    }
}