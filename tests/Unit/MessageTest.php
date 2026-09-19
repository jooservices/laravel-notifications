<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit;

use JOOservices\LaravelNotifications\Exceptions\InvalidMessageException;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class MessageTest extends TestCase
{
    public function testBuildsImmutableMessageWithHelpers(): void
    {
        $message = Message::make('Alert', 'Body line')
            ->html('<p>Body</p>')
            ->withContext(['source' => 'javdb'])
            ->recipients(['ops@example.test']);

        self::assertSame('Alert', $message->subject);
        self::assertSame('Body line', $message->body);
        self::assertSame('<p>Body</p>', $message->htmlBody);
        self::assertSame(['source' => 'javdb'], $message->context);
        self::assertSame(['ops@example.test'], $message->to);
    }

    public function testRejectsEmptySubject(): void
    {
        $this->expectException(InvalidMessageException::class);
        Message::make('  ', 'body');
    }

    public function testRejectsEmptyBodyAndHtml(): void
    {
        $this->expectException(InvalidMessageException::class);
        Message::make('Subject', '  ');
    }

    public function testRejectsNonScalarContext(): void
    {
        $this->expectException(InvalidMessageException::class);
        /** @phpstan-ignore-next-line intentional invalid fixture */
        Message::make('Subject', 'Body', context: ['nested' => ['x' => 1]]);
    }

    public function testPlainTextFallsBackToHtml(): void
    {
        $message = Message::make('Subject', '', htmlBody: '<b>Hi</b>');
        self::assertSame('Hi', $message->plainText());
    }
}
