<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit;

use JOOservices\LaravelNotifications\ChannelRegistry;
use JOOservices\LaravelNotifications\Exceptions\InvalidConfigurationException;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\NotificationManager;
use JOOservices\LaravelNotifications\SendResult;
use JOOservices\LaravelNotifications\Tests\Support\FakeChannel;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class NotificationManagerTest extends TestCase
{
    public function testSendsToAllChannelsAndContinuesAfterFailure(): void
    {
        $registry = new ChannelRegistry();
        $ok = new FakeChannel('telegram');
        $fail = new FakeChannel('slack', SendResult::STATUS_FAILED, 'boom');
        $skipped = new FakeChannel('mail', SendResult::STATUS_SKIPPED, 'no recipients');
        $registry->register($ok);
        $registry->register($fail);
        $registry->register($skipped);

        $manager = new NotificationManager($registry, ['telegram']);
        self::assertSame($registry, $manager->registry());

        $report = $manager->send(
            Message::make('Subject', 'Body'),
            ['telegram', 'slack', 'mail'],
        );

        self::assertTrue($report->hasFailures());
        self::assertCount(1, $ok->sent);
        self::assertCount(1, $fail->sent);
        self::assertCount(1, $skipped->sent);

        $telegram = $report->forChannel('telegram');
        $slack = $report->forChannel('slack');
        $mail = $report->forChannel('mail');
        self::assertNotNull($telegram);
        self::assertNotNull($slack);
        self::assertNotNull($mail);
        self::assertTrue($telegram->isSent());
        self::assertTrue($slack->isFailed());
        self::assertTrue($mail->isSkipped());
        self::assertSame('boom', $slack->reason);
    }

    public function testUsesDefaultChannelsWhenOmitted(): void
    {
        $registry = new ChannelRegistry();
        $channel = new FakeChannel('telegram');
        $registry->register($channel);

        $manager = new NotificationManager($registry, ['telegram']);
        $report = $manager->send(Message::make('Subject', 'Body'));

        self::assertTrue($report->allSuccessful());
        self::assertCount(1, $channel->sent);
    }

    public function testRejectsEmptyChannelList(): void
    {
        $manager = new NotificationManager(new ChannelRegistry(), []);

        $this->expectException(InvalidConfigurationException::class);
        $manager->send(Message::make('Subject', 'Body'));
    }
}
