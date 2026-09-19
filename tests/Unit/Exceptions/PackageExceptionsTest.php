<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit\Exceptions;

use JOOservices\LaravelNotifications\Exceptions\ChannelNotRegisteredException;
use JOOservices\LaravelNotifications\Exceptions\InvalidConfigurationException;
use JOOservices\LaravelNotifications\Exceptions\InvalidMessageException;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class PackageExceptionsTest extends TestCase
{
    public function testExceptionsExposeStableErrorCodes(): void
    {
        self::assertSame(
            'laravel-notifications.channel.not_registered',
            ChannelNotRegisteredException::forChannel('x')->errorCode(),
        );
        self::assertSame(
            'laravel-notifications.message.invalid',
            InvalidMessageException::emptySubject()->errorCode(),
        );
        self::assertSame(
            'laravel-notifications.config.invalid',
            InvalidConfigurationException::emptyDefaultChannels()->errorCode(),
        );
        self::assertSame(
            'laravel-notifications.config.invalid',
            InvalidConfigurationException::invalidDefaultChannel('discord')->errorCode(),
        );
    }

    public function testWithContextCopiesException(): void
    {
        $original = ChannelNotRegisteredException::forChannel('telegram');
        $copy = $original->withContext(['extra' => 'yes']);

        self::assertNotSame($original, $copy);
        self::assertSame('telegram', $copy->getContext()['channel']);
        self::assertSame('yes', $copy->getContext()['extra']);
    }
}
