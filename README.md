# Laravel Bird Notification Channel

This package provides a Laravel notification channel for sending SMS messages using the Bird API.

## Installation

You can install this package via Composer:

```bash
composer require janyksteenbeek/laravel-bird-notifications
```

## Configuration

1. Add your Bird credentials to your `.env` file:

```env
BIRD_ACCESS_KEY=your_access_key
BIRD_WORKSPACE_ID=your_workspace_id
BIRD_CHANNEL_ID=your_channel_id
```

2. Add the configuration to `config/services.php`:

```php
'bird' => [
    'access_key' => env('BIRD_ACCESS_KEY'),
    'workspace' => env('BIRD_WORKSPACE_ID'),
    'channel' => env('BIRD_CHANNEL_ID'),
],
```

## Usage

### Basic Usage

First, create a notification class using Laravel's notification command:

```bash
php artisan make:notification OrderConfirmation
```

Then, implement the `toBird` method in your notification class:

```php
use NotificationChannels\Bird\BirdMessage;
use NotificationChannels\Bird\BirdChannel;

class OrderConfirmation extends Notification
{
    public function via($notifiable)
    {
        return [BirdChannel::class];
    }

    public function toBird($notifiable)
    {
        return (new BirdMessage())
            ->setBody("Your order #{$this->order->id} has been confirmed!")
            ->setRecipients($notifiable->phone_number);
    }
}
```

### Using BirdRoute for Dynamic Configuration

BirdRoute allows you to customize the Bird configuration per notification. This is useful when you need to:
- Send to multiple recipients
- Use different access tokens
- Use different workspaces
- Use different channels

#### Method 1: Using BirdRoute in the Notifiable Model

```php
use NotificationChannels\Bird\BirdRoute;

class User extends Authenticatable
{
    public function routeNotificationForBird($notification)
    {
        return BirdRoute::make(
            recipients: [$this->phone_number],
            token: 'custom-access-token',        // optional
            workspace: 'custom-workspace-id',     // optional
            channel: 'custom-channel-id'         // optional
        );
    }
}
```

#### Method 2: Using BirdRoute in the Notification

```php
use NotificationChannels\Bird\BirdMessage;
use NotificationChannels\Bird\BirdRoute;

class OrderConfirmation extends Notification
{
    public function toBird($notifiable)
    {
        // Create a BirdRoute instance
        $route = BirdRoute::make(
            recipients: ['+31612345678', '+31687654321'],
            workspace: 'special-workspace-id',
            channel: 'urgent-channel-id'
        );

        // Create your message
        $message = (new BirdMessage())
            ->setBody("Your order #{$this->order->id} has been confirmed!");

        // Apply the route configuration
        if ($route->token) {
            $message->setAccessToken($route->token);
        }
        $message->setRecipients($route->recipients);

        return $message;
    }
}
```

### Sending to Multiple Recipients

```php
public function toBird($notifiable)
{
    return (new BirdMessage())
        ->setBody('Your order has been confirmed!')
        ->setRecipients([
            '+31612345678',
            '+31687654321'
        ]);
}
```


## Security

If you discover any security-related issues, please email security@example.com instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)

Special thanks to https://github.com/laravel-notification-channels/messagebird for providing a good base.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 