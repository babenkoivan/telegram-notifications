# Telegram Notifications For Laravel

The package provides easy way to send [Telegram](https://telegram.org/) notifications to any [notifiable entity](https://laravel.com/docs/5.3/notifications#sending-notifications) in your project.
It uses official [Telegram Bot API](https://core.telegram.org/bots/api) to deliver your message directly to a user.
You can send any information you want: text, media, location or contact. 

## Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Set up your model](#set-up-your-model)
* [Simple example](#simple-example)
* [Advanced Usage](#advanced-usage)

## Requirements

The package was tested on following configuration: 

* PHP version &gt;= 7.0
* Laravel Framework version &gt;= 5.4

## Installation

To install package you can use composer:
```
composer require babenkoivan/telegram-notifications
```

Once you've installed package, you need to register service provider in ```config/app.php``` file:
```php
'providers' => [
    TelegramNotifications\TelegramServiceProvider::class    
]
``` 

To copy package settings to ```config``` directory run:
```
php artisan vendor:publish --provider='TelegramNotifications\TelegramServiceProvider'
```
Now you're ready to set up bot token for your application. 
If you haven't created a bot you can make new one using [BotFather](https://telegram.me/botfather). 
For more information, visit [Bots: An introduction for developers](https://core.telegram.org/bots) page.
 
Let's move on and assume you have bot token now. 
You can configure token either in ```.env``` file:
```
TELEGRAM_BOT_TOKEN=335237666:FFF45pYTYm9HkKWByaepSpcKAUWMo2uMF_9
```
or in ```config/telegram.php``` file:
```php
<?php

return [
    'bot_token' => '335237666:FFF45pYTYm9HkKWByaepSpcKAUWMo2uMF_9',
];
```

Of course, token above is just an example, you have to specify your own token.

## Set up your model

To notify user or any other notifiable entity you need to use ```Notifiable``` trait on your model and define routeNotificationForTelegram, which will return ```chat_id```:
```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    // ...

    public function routeNotificationForTelegram()
    {
        return '993344556';
    }
}
```

At this point, you may wonder where to get ```chat_id```.
The answer is it's up to you! 
You can create a [webhook](https://core.telegram.org/bots/api#setwebhook) to receive updates from you bot and collect users' ids or you can specify ids manually for certain users.

To get started, you can write simple ```Hello!``` message to your bot and get details by requesting [API method](https://core.telegram.org/bots/api#getupdates):
```
curl https://api.telegram.org/bot<your_token_here>/getUpdates
```

You will receive json in return:
```json
{
    "ok": true,
    "result": [
        {
            "message": {
                "chat": {
                    "id": 993344556 // this is what we were looking for 
                    // ...
                }
            }
        }
    ]
}
```

## Simple example

if you [installed the package](#installation) and [configured a model](#set-up-your-model) you're ready to make your first Telegram notification.
You can create new notification by running ```artisan``` command:
```
php artisan make:notification TelegramNotification
```

And again, ```TelegramNotification``` here is just an example you can specify any name you want.

Now, you can go to ```app/Notifications``` folder and you'll see ```TelegramNotification.php``` file. 
In ```via``` method specify ```TelegramChannel::class``` and initialize new ```TelegramMessage``` instance in ```toTelegram``` method:
```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use TelegramNotifications\TelegramChannel;
use TelegramNotifications\Messages\TelegramMessage;

class TelegramNotification extends Notification
{
    use Queueable;

    public function via()
    {
        return [TelegramChannel::class];
    }

    public function toTelegram()
    {
        return (new TelegramMessage())
            ->text('Hello, world!');
    }
}
```

To send notification use ```notify``` method with notifiable entity. 
Let's say we have an authenticated user and we want to send some message right from the route callback.
We'll do it like this:

```php
<?php

use \App\Notifications\TelegramNotification;

Route::post('/', function () {
    Auth::user()->notify(new TelegramNotification());
});
```

## Advanced Usage

// other examples
