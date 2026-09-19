<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit\Channels;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Testing\TestResponse;
use JOOservices\Client\Testing\TestResponseSequence;
use JOOservices\LaravelNotifications\Channels\TelegramChannel;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class TelegramChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        ClientBuilder::clearFake();
        parent::tearDown();
    }
    public function testSkipsWhenCredentialsMissing(): void
    {
        $channel = new TelegramChannel(botToken: '', chatId: '');
        $result = $channel->send(Message::make('Subject', 'Body'));

        self::assertTrue($result->isSkipped());
    }
    public function testPostsSendMessagePayload(): void
    {
        ClientBuilder::fake()->respond(
            'POST',
            '*',
            (new TestResponseSequence())->push(TestResponse::json(['ok' => true])),
        );

        $client = ClientBuilder::create()
            ->withBaseUri('https://api.telegram.org/')
            ->build();

        $channel = new TelegramChannel(
            botToken: 'tok',
            chatId: '123',
            client: $client,
        );

        $result = $channel->send(
            Message::make('Circuit open', 'javdb failed', context: ['source_slug' => 'javdb']),
        );

        self::assertTrue($result->isSent());

        $recorded = ClientBuilder::recorded();
        self::assertCount(1, $recorded);
        self::assertStringContainsString('/bottok/sendMessage', (string) $recorded[0]->request->getUri());
        self::assertStringContainsString('Circuit open', (string) $recorded[0]->request->getBody());
        self::assertStringContainsString('source_slug: javdb', (string) $recorded[0]->request->getBody());
    }

    public function testPostsWhenBotTokenContainsColon(): void
    {
        ClientBuilder::fake()->respond(
            'POST',
            '*',
            (new TestResponseSequence())->push(TestResponse::json(['ok' => true])),
        );

        $client = ClientBuilder::create()
            ->withBaseUri('https://api.telegram.org/')
            ->build();

        $channel = new TelegramChannel(
            botToken: '123456:ABC-DEF',
            chatId: '42',
            client: $client,
        );

        $result = $channel->send(Message::make('Subject', 'Body'));

        self::assertTrue($result->isSent(), (string) $result->reason);
        $recorded = ClientBuilder::recorded();
        self::assertCount(1, $recorded);
        self::assertSame(
            'https://api.telegram.org/bot123456:ABC-DEF/sendMessage',
            (string) $recorded[0]->request->getUri(),
        );
    }
    public function testMarksFailedOnNonSuccessStatus(): void
    {
        ClientBuilder::fake()->respond(
            'POST',
            '*',
            (new TestResponseSequence())->push(TestResponse::make(500)),
        );

        $client = ClientBuilder::create()->withBaseUri('https://api.telegram.org/')->build();
        $channel = new TelegramChannel(botToken: 'tok', chatId: '123', client: $client);

        $result = $channel->send(Message::make('Subject', 'Body'));

        self::assertTrue($result->isFailed());
        self::assertStringContainsString('HTTP 500', (string) $result->reason);
    }
}
