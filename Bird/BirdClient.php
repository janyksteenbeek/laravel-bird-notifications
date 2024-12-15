<?php

namespace NotificationChannels\Bird;

use Exception;
use GuzzleHttp\Client;
use NotificationChannels\Bird\Exceptions\CouldNotSendNotification;

class BirdClient
{
    protected $client;
    protected $access_key;
    protected $workspace;
    protected $channel;

    public function __construct(Client $client, string $access_key, string $workspace, string $channel)
    {
        $this->client = $client;
        $this->access_key = $access_key;
        $this->workspace = $workspace;
        $this->channel = $channel;
    }

    /**
     * Set the workspace ID for this request.
     */
    public function setWorkspace(string $workspace): self
    {
        $this->workspace = $workspace;
        return $this;
    }

    /**
     * Set the channel ID for this request.
     */
    public function setChannel(string $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

    public function send(BirdMessage $message)
    {
        $accessToken = $message->accessToken ?? $this->access_key;

        try {
            $payload = [
                'body' => [
                    'type' => 'text',
                    'text' => [
                        'text' => $message->body,
                    ],
                ],
                'receiver' => [
                    'contacts' => array_map(function($recipient) {
                        return [
                            'identifierValue' => $recipient,
                            'identifierKey' => 'phonenumber',
                        ];
                    }, explode(',', $message->recipients)),
                ],
            ];

            $response = $this->client->request('POST', "https://api.bird.com/workspaces/{$this->workspace}/channels/{$this->channel}/messages", [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'AccessKey ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->__toString());
        } catch (Exception $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
        }
    }
}
