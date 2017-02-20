<?php

namespace TelegramNotifications\Messages;

class TelegramVenue extends TelegramEntity
{
    protected $required = [
        'latitude',
        'longitude',
        'title',
        'address'
    ];

    protected $optional = [
        'foursquare_id',
        'disable_notification'
    ];
}