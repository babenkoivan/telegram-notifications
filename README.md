# Telegram Notifications For Laravel

[![Packagist](https://img.shields.io/packagist/v/babenkoivan/telegram-notifications.svg)](https://packagist.org/packages/babenkoivan/telegram-notifications)
[![Packagist](https://img.shields.io/packagist/dt/babenkoivan/telegram-notifications.svg)](https://packagist.org/packages/babenkoivan/telegram-notifications)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/babenkoivan/telegram-notifications)


The package provides easy way to send [Telegram](https://telegram.org/) notifications to any [notifiable entity](https://laravel.com/docs/5.3/notifications#sending-notifications) in your project.
It uses official [Telegram Bot API](https://core.telegram.org/bots/api) to deliver your message directly to a user.
You can send any information you want: text, media, location or contact. 

## Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Set up your model](#set-up-your-model)
* [Usage example](#usage-example)
* [Advanced Usage](#advanced-usage)
    * [Single Message](#singlemessage)
        * [TelegramMessage](#telegrammessage)
        * [TelegramPhoto](#telegramphoto)
        * [TelegramAudio](#telegramaudio)
        * [TelegramDocument](#telegramdocument)
        * [TelegramSticker](#telegramsticker)
        * [TelegramVideo](#telegramvideo)
        * [TelegramVoice](#telegramvoice)
        * [TelegramLocation](#telegramlocation)
        * [TelegramVenue](#telegramvenue)
        * [TelegramContact](#telegramcontact)
    * [Message Collection](#message-collection)

## Requirements

The package has been tested on following configuration: 

* PHP version &gt;= 7.3
* Laravel Framework version &gt;= 5.5

## Installation

To install the package you can use composer:

```
composer require babenkoivan/telegram-notifications
```

If the package discovery is disabled, you need to register the service provider in `config/app.php` file:

```php
'providers' => [
    TelegramNotifications\TelegramServiceProvider::class    
]
``` 

To copy the package settings to `config` directory run:

```
php artisan vendor:publish --provider='TelegramNotifications\TelegramServiceProvider'
```

Now you're ready to set up a bot token for your application. 
If you haven't created a bot you can make new one using [BotFather](https://telegram.me/botfather). 
For more information, visit [Bots: An introduction for developers](https://core.telegram.org/bots) page.
 
Let's move on and assume you have a token. 
You can configure the token either in `.env` file:

```
TELEGRAM_BOT_TOKEN=335237666:FFF45pYTYm9HkKWByaepSpcKAUWMo2uMF_9
```

or in `config/telegram.php` file:

```php
<?php

return [
    'bot_token' => '335237666:FFF45pYTYm9HkKWByaepSpcKAUWMo2uMF_9',
];
```

Of course, the token above is just an example, you have to specify your own token.

## Set up your model

To notify user or any other notifiable entity you need to use `Notifiable` trait with your model and define `routeNotificationForTelegram` method, which will return a `chat_id`:

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
        return 993344556;
    }
}
```

At this point, you may wonder where to get a `chat_id`. The answer is it's up to you! 
You can create a [webhook](https://core.telegram.org/bots/api#setwebhook) to receive updates for your bot and collect chat ids, or you can specify ids manually for certain users.

To get started, you can send `Hello!` message to your bot and then get message details by requesting [API method](https://core.telegram.org/bots/api#getupdates):

```
curl https://api.telegram.org/bot<your_token_here>/getUpdates
```

You will receive a JSON in return:

```
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

## Usage example

if you [installed the package](#installation) and [configured a model](#set-up-your-model) you're ready to make your first Telegram notification.
You can create a new notification using `artisan` command:

```
php artisan make:notification TelegramNotification
```

And again, `TelegramNotification` here is just an example, you can specify any name you want.

Now, you can go to `app/Notifications` folder and you'll see `TelegramNotification.php` file. 
In `via` method specify `TelegramChannel::class` and initialize a new `TelegramMessage` instance in `toTelegram` method:

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
        return (new TelegramMessage())->text('Hello, world!');
    }
}
```

To send the notification use `notify` method with notifiable entity. 

Let's say we have an authenticated user and we want to send a message from a route callback.
We can do it like this:

```php
<?php

use \App\Notifications\TelegramNotification;

Route::post('/', function () {
    Auth::user()->notify(new TelegramNotification());
});
```

## Advanced Usage

You can send either a single message or a message collection at once.
 
### Single Message

Each message class represents certain type of information you can deliver to a user.
To send a message return a new instance of necessary type from `toTelegram` method:

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
        // to set any required or optional field use
        // setter, which name is field name in camelCase
        return (new TelegramMessage())
            ->text('Hello, world!')
            ->disableNotification(true);
    }
}
```

You can also pass parameters to the constructor, to be more explicit:

```php
<?php

new TelegramMessage([
    'text' => 'Hello, world!',
    'disable_notification' => true
]);
```

Available message types are listed below.


#### TelegramMessage

`TelegramNotifications\Messages\TelegramMessage`

Field | Type | Description | Required
--- | --- | --- | ---
text | String | Text of the message to be sent | Yes
parse_mode | String | Send [Markdown](https://core.telegram.org/bots/api#markdown-style) or [HTML](https://core.telegram.org/bots/api#html-style), if you want Telegram apps to show [bold, italic, fixed-width text or inline URLs](https://core.telegram.org/bots/api#formatting-options) in your bot's message | Optional
disable_web_page_preview | Boolean | Disables link previews for links in this message | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramPhoto

`TelegramNotifications\Messages\TelegramPhoto`

Field | Type | Description | Required
--- | --- | --- | ---
photo | String | Photo to send. Pass a file_id as String to send a photo that exists on the Telegram servers, pass an HTTP URL as a String for Telegram to get a photo from the Internet. [More about sending files](https://core.telegram.org/bots/api#sending-files) | Yes
caption | String | Photo caption, 0-200 characters | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramAudio

`TelegramNotifications\Messages\TelegramAudio`

Field | Type | Description | Required
--- | --- | --- | ---
audio | String | Audio file to send. Pass a file_id as String to send a photo that exists on the Telegram servers, pass an HTTP URL as a String for Telegram to get a photo from the Internet. [More about sending files](https://core.telegram.org/bots/api#sending-files) | Yes
caption | String | Audio caption, 0-200 characters | Optional
duration | Integer | Duration of the audio in seconds | Optional
performer | String | Performer | Optional
title | String | Track name | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramDocument

`TelegramNotifications\Messages\TelegramDocument`

Field | Type | Description | Required
--- | --- | --- | ---
document | String | File to send. Pass a file_id as String to send a photo that exists on the Telegram servers, pass an HTTP URL as a String for Telegram to get a photo from the Internet. [More about sending files](https://core.telegram.org/bots/api#sending-files) | Yes
caption | String | Document caption, 0-200 characters | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramSticker

`TelegramNotifications\Messages\TelegramSticker`

Field | Type | Description | Required
--- | --- | --- | ---
sticker | String | Sticker to send. Pass a file_id as String to send a photo that exists on the Telegram servers, pass an HTTP URL as a String for Telegram to get a photo from the Internet. [More about sending files](https://core.telegram.org/bots/api#sending-files) | Yes
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramVideo

`TelegramNotifications\Messages\TelegramVideo`

Field | Type | Description | Required
--- | --- | --- | ---
video | String | Video to send. Pass a file_id as String to send a photo that exists on the Telegram servers, pass an HTTP URL as a String for Telegram to get a photo from the Internet. [More about sending files](https://core.telegram.org/bots/api#sending-files) | Yes
duration | Integer | Duration of sent video in seconds | Optional
width | Integer | Video width | Optional
height | Integer | Video height | Optional
caption | String | Video caption, 0-200 characters | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramVoice

`TelegramNotifications\Messages\TelegramVoice`

Field | Type | Description | Required
--- | --- | --- | ---
voice | String | Audio file to send. Pass a file_id as String to send a photo that exists on the Telegram servers, pass an HTTP URL as a String for Telegram to get a photo from the Internet. [More about sending files](https://core.telegram.org/bots/api#sending-files) | Yes
caption | String | Voice message caption, 0-200 characters | Optional
duration | Integer | Duration of the voice message in seconds | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramLocation

`TelegramNotifications\Messages\TelegramLocation`

Field | Type | Description | Required
--- | --- | --- | ---
latitude | Float number | Latitude of location | Yes
longitude | Float number | Longitude of location | Yes
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramVenue

`TelegramNotifications\Messages\TelegramVenue`

Field | Type | Description | Required
--- | --- | --- | ---
latitude | Float number | Latitude of the venue | Yes
longitude | Float number | Longitude of the venue | Yes
title | String | Name of the venue | Yes
address | String | Address of the venue | Yes
foursquare_id | String | Foursquare identifier of the venue | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

#### TelegramContact

`TelegramNotifications\Messages\TelegramContact`

Field | Type | Description | Required
--- | --- | --- | ---
phone_number | String | Contact's phone number | Yes
first_name | String | Contact's first name | Yes
last_name | String | Contact's last name | Optional
disable_notification | Boolean | Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound | Optional

### Message Collection

Instead of sending one message at once you can send bunch of messages using `TelegramCollection`:

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use TelegramNotifications\TelegramChannel;
use TelegramNotifications\Messages\TelegramCollection;

class TelegramNotification extends Notification
{
    use Queueable;

    public function via()
    {
        return [TelegramChannel::class];
    }

    public function toTelegram()
    {
        return (new TelegramCollection())
            ->message(['text' => 'Hello, world!'])
            ->location(['latitude' => 55.755768, 'longitude' => 37.617671])
            // ...
            ->sticker(['sticker' => 'CAADBQADJwEAAl7ylwK4Q0M5P7UxhQI']);
    }
}
```

Each method of the collection creates corresponding message instance and puts it in the collection.
Available methods are listed below:

Method | Corresponding entity
--- | ---
message | [TelegramMessage](#telegrammessage)
photo | [TelegramPhoto](#telegramphoto)
audio | [TelegramAudio](#telegramaudio)
document | [TelegramDocument](#telegramdocument)
sticker | [TelegramSticker](#telegramsticker)
video | [TelegramVideo](#telegramvideo)
voice | [TelegramVoice](#telegramvoice)
location | [TelegramLocation](#telegramlocation)
venue | [TelegramVenue](#telegramvenue)
contact | [TelegramContact](#telegramcontact)
