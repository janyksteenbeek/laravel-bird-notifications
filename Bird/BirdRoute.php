<?php

namespace NotificationChannels\Bird;

class BirdRoute
{
    /**
     * The token to use for sending the notification.
     *
     * Overrides the config-defined token (if any).
     */
    public ?string $token;

    /**
     * The workspace to use for sending the notification.
     *
     * Overrides the config-defined workspace (if any).
     */
    public ?string $workspace;

    /**
     * The channel to use for sending the notification.
     *
     * Overrides the config-defined channel (if any).
     */
    public ?string $channel;

    /**
     * The recipients of the message.
     */
    public array $recipients;

    /**
     * Create a new Bird route instance.
     */
    public function __construct(
        array $recipients,
        string $token = null,
        string $workspace = null,
        string $channel = null
    ) {
        $this->token = $token;
        $this->workspace = $workspace;
        $this->channel = $channel;
        $this->recipients = $recipients;
    }

    /**
     * Fluently create a new Bird route instance.
     */
    public static function make(
        array $recipients,
        string $token = null,
        string $workspace = null,
        string $channel = null
    ): self {
        return new static($recipients, $token, $workspace, $channel);
    }
}
