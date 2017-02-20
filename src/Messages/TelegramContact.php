<?php

namespace TelegramNotifications\Messages;

class TelegramContact extends TelegramEntity
{
    protected $required = [
        'phone_number',
        'first_name',
    ];

    protected $optional = [
        'last_name',
        'disable_notification'
    ];
}