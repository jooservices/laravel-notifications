<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use JOOservices\LaravelNotifications\LaravelNotificationsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelNotificationsServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        /** @var ConfigRepository $config */
        $config = $app->make('config');
        $config->set('laravel-notifications.default_channels', ['telegram']);
        $config->set('laravel-notifications.channels.telegram.bot_token', '');
        $config->set('laravel-notifications.channels.telegram.chat_id', '');
        $config->set('laravel-notifications.channels.slack.webhook_url', '');
        $config->set('laravel-notifications.channels.mail.to', []);
        $config->set('mail.default', 'array');
        $config->set('mail.mailers.array', ['transport' => 'array']);
    }
}
