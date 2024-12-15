<?php

namespace NotificationChannels\Bird;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\Bird\Exceptions\InvalidConfiguration;

class BirdServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(BirdChannel::class)
            ->needs(BirdClient::class)
            ->give(function () {
                $config = config('services.bird');

                if (is_null($config)) {
                    throw InvalidConfiguration::configurationNotSet();
                }

                if (! isset($config['access_key'], $config['workspace'], $config['channel'])) {
                    throw InvalidConfiguration::configurationNotSet();
                }

                return new BirdClient(
                    new Client(),
                    $config['access_key'],
                    $config['workspace'],
                    $config['channel']
                );
            });
    }
}
