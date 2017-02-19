<?php

namespace TelegramNotifications\Messages;

class TelegramMessage extends TelegramEntity
{
    protected $required = [
        'text'
    ];

    protected $optional = [
        'parse_mode',
        'disable_web_page_preview',
        'disable_notification'
    ];
}