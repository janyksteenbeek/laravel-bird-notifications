# Bird notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/messagebird.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/messagebird)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/messagebird/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/messagebird)
[![StyleCI](https://styleci.io/repos/65683649/shield)](https://styleci.io/repos/65683649)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/357bb8d3-2163-45be-97f2-ce71434a4379.svg?style=flat-square)](https://insight.sensiolabs.com/projects/357bb8d3-2163-45be-97f2-ce71434a4379)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/messagebird.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/messagebird)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/messagebird/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/messagebird/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/messagebird.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/messagebird)

This package makes it easy to send [Bird SMS notifications](https://bird.com) with Laravel.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Setting up your Bird account](#setting-up-your-bird-account)
- [Usage](#usage)
- [Dynamic Configuration](#dynamic-configuration)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Requirements

- [Sign up](https://bird.com/signup) for a free Bird account
- Create a new access key in the developers section
- Set up a workspace and channel in your Bird account

## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/bird
```

For Laravel 5.4 or lower, you must add the service provider to your config:

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\Bird\BirdServiceProvider::class,
],
```

## Setting up your Bird account

Add the environment variables to your `config/services.php`:

```php
// config/services.php
...
'bird' => [
    'access_key' => env('BIRD_ACCESS_KEY'),
    'workspace' => env('BIRD_WORKSPACE_ID'),
    'channel' => env('BIRD_CHANNEL_ID'),
],
...
```

Add your Bird Access Key, Workspace ID, and Channel ID to your `.env`:

```php
// .env
BIRD_ACCESS_KEY=your_access_key
BIRD_WORKSPACE_ID=your_workspace_id
BIRD_CHANNEL_ID=your_channel_id
```

## Usage

Now you can use the channel in your `via()` method inside the notification:

``` php
use NotificationChannels\Bird\BirdChannel;
use NotificationChannels\Bird\BirdMessage;
use Illuminate\Notifications\Notification;

class VpsServerOrdered extends Notification
{
    public function via($notifiable)
    {
        return [BirdChannel::class];
    }

    public function toBird($notifiable)
    {
        return (new BirdMessage("Your {$notifiable->service} was ordered!"));
    }
}
```

Additionally you can add recipients (single value or array):

``` php
return (new BirdMessage("Your {$notifiable->service} was ordered!"))->setRecipients($recipients);
```

### Using BirdRoute

You can use `BirdRoute` to specify custom recipients, access token, workspace, and channel for a specific notification:

```php
use NotificationChannels\Bird\BirdRoute;

public function routeNotificationForBird()
{
    return BirdRoute::make(
        recipients: ['+31612345678', '+31687654321'],
        token: 'custom-access-token',        // optional
        workspace: 'custom-workspace-id',     // optional
        channel: 'custom-channel-id'         // optional
    );
}
```

This allows you to:
- Send to multiple recipients
- Use a different access token than your default configuration
- Use a different workspace ID than your default configuration
- Use a different channel ID than your default configuration

### Using Routes

You can also use Laravel's Route facade to send notifications to specific numbers:

```php
use Illuminate\Support\Facades\Route;

Route::notification('+31612345678', new VpsServerOrdered($vps));
```

This allows you to quickly send a notification to a specific phone number without creating a notifiable entity.

## Dynamic Configuration

You can set the access token dynamically at runtime:

```php
use NotificationChannels\Bird\BirdMessage;

$message = (new BirdMessage("Your order was processed!"))
    ->setAccessToken('your-dynamic-access-key');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
