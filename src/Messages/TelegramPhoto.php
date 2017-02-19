<?php

namespace TelegramNotifications\Messages;

class TelegramPhoto extends TelegramEntity
{
    protected $required = [
        'photo'
    ];

    protected $optional = [
        'caption',
        'disable_notification'
    ];
}