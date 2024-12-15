<?php

namespace NotificationChannels\Bird;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\Bird\Exceptions\CouldNotSendNotification;

class BirdChannel
{
    /** @var \NotificationChannels\Bird\BirdClient */
    protected $client;
    private $dispatcher;

    public function __construct(BirdClient $client, Dispatcher $dispatcher = null)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return object with response body data if succesful response from API | empty array if not
     *
     * @throws \NotificationChannels\Bird\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toBird($notifiable);

        $data = [];

        if (is_string($message)) {
            $message = BirdMessage::create($message);
        }

        if ($to = $notifiable->routeNotificationFor('bird', $notification)) {
            if($to instanceof BirdRoute) {
                if ($to->token) {
                    $message->setAccessToken($to->token);
                }
                if ($to->workspace) {
                    $this->client->setWorkspace($to->workspace);
                }
                if ($to->channel) {
                    $this->client->setChannel($to->channel);
                }
                $message->setRecipients($to->recipients);
            } else {
                $message->setRecipients($to);
            }
        }

        try {
            $data = $this->client->send($message);

            if ($this->dispatcher !== null) {
                $this->dispatcher->dispatch('bird-sms', [$notifiable, $notification, $data]);
            }
        } catch (CouldNotSendNotification $e) {
            if ($this->dispatcher !== null) {
                $this->dispatcher->dispatch(
                    new NotificationFailed(
                        $notifiable,
                        $notification,
                        'bird-sms',
                        $e->getMessage()
                    )
                );
            }
        }

        return $data;
    }
}
