<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Support\ServiceProvider;
use JOOservices\LaravelNotifications\Contracts\ChannelRegistryInterface;
use JOOservices\LaravelNotifications\Support\BuiltInChannelRegistrar;
use LogicException;

final class LaravelNotificationsServiceProvider extends ServiceProvider
{
    /**
     * @throws LogicException
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-notifications.php',
            'laravel-notifications',
        );

        $this->app->singleton(ChannelRegistry::class, function (Application $app): ChannelRegistry {
            /** @var ConfigRepository $config */
            $config = $app->make('config');
            /** @var MailFactory $mail */
            $mail = $app->make(MailFactory::class);

            $registry = new ChannelRegistry();
            (new BuiltInChannelRegistrar($config, $mail))->register($registry);

            return $registry;
        });

        $this->app->alias(ChannelRegistry::class, ChannelRegistryInterface::class);

        $this->app->singleton(NotificationManager::class, function (Application $app): NotificationManager {
            /** @var ConfigRepository $config */
            $config = $app->make('config');

            $defaults = [];
            foreach ((array) $config->get('laravel-notifications.default_channels', []) as $name) {
                if (is_string($name) && $name !== '') {
                    $defaults[] = $name;
                }
            }

            /** @var ChannelRegistry $registry */
            $registry = $app->make(ChannelRegistry::class);

            return new NotificationManager(
                registry: $registry,
                defaultChannels: $defaults,
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-notifications.php' => config_path('laravel-notifications.php'),
            ], 'config');
        }
    }
}
