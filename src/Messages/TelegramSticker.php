<?php

namespace TelegramNotifications\Messages;

class TelegramSticker extends TelegramEntity
{
    protected $required = [
        'sticker'
    ];

    protected $optional = [
        'disable_notification'
    ];
}