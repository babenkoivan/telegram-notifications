<?php

namespace TelegramNotifications\Messages;

class TelegramVoice extends TelegramEntity
{
    protected $required = [
        'voice'
    ];

    protected $optional = [
        'caption',
        'duration',
        'disable_notification'
    ];
}