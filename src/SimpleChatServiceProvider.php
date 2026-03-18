<?php

namespace SimpleChat;

use Illuminate\Support\ServiceProvider;
use SimpleChat\Contracts\ChatDriver;

class SimpleChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/simple-chat.php', 'simple-chat');

        $this->app->singleton(ChatDriver::class, function ($app) {
            $config = $app['config']['simple-chat'];
            $driverName = $config['default'];
            $driverConfig = $config['drivers'][$driverName] ?? null;

            if (!$driverConfig) {
                throw new \Exception("Chat driver [{$driverName}] is not configured.");
            }

            $driverClass = $driverConfig['driver'];
            return new $driverClass($driverConfig);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'simple-chat');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'simple-chat');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if (config('simple-chat.broadcasting.enabled', false) && class_exists(\Illuminate\Broadcasting\BroadcastServiceProvider::class)) {
            require __DIR__ . '/../routes/channels.php';
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/simple-chat.php' => config_path('simple-chat.php'),
            ], 'simple-chat-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/simple-chat'),
            ], 'simple-chat-views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/simple-chat'),
            ], 'simple-chat-translations');

            $this->publishes([
                __DIR__ . '/../routes/channels.php' => base_path('routes/simple-chat-channels.php'),
            ], 'simple-chat-channels');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }
}
