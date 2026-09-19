<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Feature;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use JOOservices\LaravelNotifications\ChannelRegistry;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\NotificationManager;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class ConfiguredChannelsTest extends TestCase
{
    public function testBuiltInRegistrarWiresConfiguredMailChannel(): void
    {
        $app = $this->app;
        self::assertInstanceOf(Application::class, $app);

        /** @var ConfigRepository $config */
        $config = $app->make('config');
        $config->set('laravel-notifications.channels.mail.to', ['ops@example.test']);
        $config->set('laravel-notifications.channels.mail.from.address', 'alerts@example.test');
        $config->set('laravel-notifications.default_channels', ['mail']);

        $app->forgetInstance(ChannelRegistry::class);
        $app->forgetInstance(NotificationManager::class);

        /** @var NotificationManager $manager */
        $manager = $app->make(NotificationManager::class);
        $report = $manager->send(Message::make('Subject', 'Body line'));

        self::assertFalse($report->hasFailures());
        $mail = $report->forChannel('mail');
        self::assertNotNull($mail);
        self::assertTrue($mail->isSent());
    }
}
