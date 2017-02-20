<?php

namespace TelegramNotifications\Messages;

class TelegramLocation extends TelegramEntity
{
    protected $required = [
        'latitude',
        'longitude'
    ];

    protected $optional = [
        'disable_notification',
    ];
}