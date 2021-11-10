<?php

namespace TelegramNotifications\Messages;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class TelegramEntity
{
    protected $required = [];

    protected $optional = [];

    protected $parameters = [];

    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function __call($method, $args)
    {
        $parameter = Str::snake($method);

        if ((in_array($parameter, $this->required) || in_array($parameter, $this->optional)) && count($args) == 1) {
            $this->parameters[$parameter] = reset($args);
        }

        return $this;
    }

    public function validate()
    {
        Validator::make(
            $this->parameters,
            array_combine(
                $this->required,
                array_pad([], count($this->required), 'required')
            )
        )->validate();
    }

    public function toArray()
    {
        return $this->parameters;
    }
}
