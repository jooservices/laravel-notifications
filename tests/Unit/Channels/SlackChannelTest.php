<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit\Channels;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Testing\TestResponse;
use JOOservices\Client\Testing\TestResponseSequence;
use JOOservices\LaravelNotifications\Channels\SlackChannel;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class SlackChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        ClientBuilder::clearFake();
        parent::tearDown();
    }
    public function testSkipsWhenWebhookMissing(): void
    {
        $result = (new SlackChannel(''))->send(Message::make('Subject', 'Body'));
        self::assertTrue($result->isSkipped());
    }
    public function testPostsJsonTextToWebhook(): void
    {
        ClientBuilder::fake()->respond(
            'POST',
            '*',
            (new TestResponseSequence())->push(TestResponse::make(200)),
        );

        $client = ClientBuilder::create()->build();
        $channel = new SlackChannel(
            webhookUrl: 'https://hooks.slack.test/services/T/B/X',
            client: $client,
        );

        $result = $channel->send(Message::make('Subject', 'Hello Slack'));

        self::assertTrue($result->isSent());
        $recorded = ClientBuilder::recorded();
        self::assertCount(1, $recorded);
        self::assertStringContainsString('Hello Slack', (string) $recorded[0]->request->getBody());
    }
}
