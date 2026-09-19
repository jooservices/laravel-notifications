<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit;

use JOOservices\LaravelNotifications\ChannelRegistry;
use JOOservices\LaravelNotifications\Exceptions\ChannelNotRegisteredException;
use JOOservices\LaravelNotifications\Tests\Support\FakeChannel;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class ChannelRegistryTest extends TestCase
{
    public function testRegistersAndResolvesChannels(): void
    {
        $registry = new ChannelRegistry();
        $registry->register(new FakeChannel('telegram'));

        self::assertTrue($registry->has('telegram'));
        self::assertSame(['telegram'], $registry->names());
        self::assertSame('telegram', $registry->get('telegram')->name());
    }

    public function testThrowsForUnknownChannel(): void
    {
        $registry = new ChannelRegistry();

        $this->expectException(ChannelNotRegisteredException::class);
        $registry->get('discord');
    }
}
