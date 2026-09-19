<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Facades;

use Illuminate\Support\Facades\Facade;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\NotificationManager;
use JOOservices\LaravelNotifications\SendReport;

/**
 * @method static SendReport send(Message $message, list<string>|null $channels = null)
 * @method static NotificationManager getFacadeRoot()
 *
 * @see NotificationManager
 */
final class Notification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NotificationManager::class;
    }
}
