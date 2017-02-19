<?php

namespace TelegramNotifications\Messages;

class TelegramAudio extends TelegramEntity
{
    protected $required = [
        'audio'
    ];

    protected $optional = [
        'caption',
        'duration',
        'performer',
        'title',
        'disable_notification'
    ];
}