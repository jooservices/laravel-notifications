<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Feature;

use JOOservices\LaravelNotifications\Facades\Notification;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\NotificationManager;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class NotificationFacadeTest extends TestCase
{
    public function testResolvesManagerAndSkipsUnconfiguredChannels(): void
    {
        self::assertInstanceOf(NotificationManager::class, Notification::getFacadeRoot());

        $report = Notification::send(
            Message::make('Subject', 'Body'),
            ['telegram', 'slack', 'mail'],
        );

        self::assertCount(3, $report->results);
        self::assertTrue($report->forChannel('telegram')?->isSkipped());
        self::assertTrue($report->forChannel('slack')?->isSkipped());
        self::assertTrue($report->forChannel('mail')?->isSkipped());
        self::assertFalse($report->hasFailures());
    }
    public function testPackageHasNoBladeViews(): void
    {
        $views = dirname(__DIR__, 2) . '/resources/views';
        self::assertDirectoryDoesNotExist($views);
    }
}
