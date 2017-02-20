<?php

namespace TelegramNotifications\Messages;

class TelegramVideo extends TelegramEntity
{
    protected $required = [
        'video'
    ];

    protected $optional = [
        'duration',
        'width',
        'height',
        'caption',
        'disable_notification'
    ];
}