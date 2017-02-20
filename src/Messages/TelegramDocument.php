<?php

namespace TelegramNotifications\Messages;

class TelegramDocument extends TelegramEntity
{
    protected $required = [
        'document'
    ];

    protected $optional = [
        'caption',
        'disable_notification'
    ];
}