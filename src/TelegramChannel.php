<?php

namespace TelegramNotifications;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Config;
use TelegramNotifications\Messages\TelegramCollection;
use TelegramNotifications\Messages\TelegramEntity;

class TelegramChannel
{
    private $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => sprintf('https://api.telegram.org/bot%s/', Config::get('telegram.bot_token'))
        ]);
    }

    private function makeUri(TelegramEntity $message)
    {
        return 'send'.(str_replace('Telegram', '', class_basename($message)));
    }

    private function buildPayload($chatId, TelegramEntity $message)
    {
        return [
            'json' => array_merge(
                $message->toArray(),
                ['chat_id' => $chatId]
            )
        ];
    }

    public function send($notifiable, Notification $notification)
    {
        if (!$chatId = $notifiable->routeNotificationFor('telegram')) {
            return;
        }

        $entity = $notification->toTelegram($notifiable);
        $collection = $entity instanceof TelegramCollection ? $entity : new TelegramCollection([$entity]);

        $collection
            ->validate()
            ->map(function(TelegramEntity $message) use ($chatId) {
                $this->http->post(
                    $this->makeUri($message),
                    $this->buildPayload($chatId, $message)
                );
            });
    }
}