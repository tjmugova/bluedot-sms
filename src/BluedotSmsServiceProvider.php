<?php

namespace Tjmugova\BluedotSms;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Tjmugova\BluedotSms\Channels\BluedotSmsChannel;

class BluedotSmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BluedotSms::class, function (Application $app) {
            return new BluedotSms(
                $app->make(Factory::class),
                $app['config']['bluedot-sms']
            );
        });
        $this->mergeConfigFrom(
            __DIR__ . '/../config/bluedot-sms.php',
            'bluedot-sms'
        );
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('bluedotSms', function ($app) {
                return new BluedotSmsChannel(
                    $app->make(BluedotSms::class),
                    $app['config']['bluedot-sms']['sms_from'],
                    $app->make(Dispatcher::class),
                );
            });
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/bluedot-sms.php' => $this->app->configPath('bluedot-sms.php'),
            ], 'bluedot-sms');
        }
    }
}