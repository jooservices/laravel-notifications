<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit\Channels;

use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Foundation\Application;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\Transport\ArrayTransport;
use JOOservices\LaravelNotifications\Channels\MailChannel;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\Tests\TestCase;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;

final class MailChannelTest extends TestCase
{
    public function testSkipsWhenRecipientsMissing(): void
    {
        $channel = new MailChannel($this->mailFactory());
        $result = $channel->send(Message::make('Subject', 'Body'));

        self::assertTrue($result->isSkipped());
        self::assertCount(0, $this->sentMessages());
    }

    public function testSendsRawPreRenderedContent(): void
    {
        $channel = new MailChannel(
            mail: $this->mailFactory(),
            defaultTo: ['ops@example.test'],
            from: ['address' => 'alerts@example.test', 'name' => 'Alerts'],
        );

        $result = $channel->send(Message::make('Crawl alert', 'Source unhealthy'));

        self::assertTrue($result->isSent(), (string) $result->reason);
        $messages = $this->sentMessages();
        self::assertCount(1, $messages);
        $email = $messages[0]->getOriginalMessage();
        self::assertInstanceOf(Email::class, $email);
        self::assertSame('Crawl alert', $email->getSubject());
        self::assertStringContainsString('Source unhealthy', $messages[0]->toString());
    }

    public function testSendsHtmlWhenProvided(): void
    {
        $channel = new MailChannel(
            mail: $this->mailFactory(),
            defaultTo: ['ops@example.test'],
        );

        $result = $channel->send(
            Message::make('Crawl alert', 'plain')
                ->html('<strong>Source unhealthy</strong>'),
        );

        self::assertTrue($result->isSent(), (string) $result->reason);
        $messages = $this->sentMessages();
        self::assertCount(1, $messages);
        self::assertStringContainsString('Source unhealthy', $messages[0]->toString());
    }

    public function testUsesMessageRecipientsOverDefaults(): void
    {
        $channel = new MailChannel(
            mail: $this->mailFactory(),
            defaultTo: ['default@example.test'],
        );

        $result = $channel->send(
            Message::make('Subject', 'Body')->recipients(['override@example.test']),
        );

        self::assertTrue($result->isSent(), (string) $result->reason);
        $messages = $this->sentMessages();
        self::assertCount(1, $messages);
        self::assertStringContainsString('override@example.test', $messages[0]->toString());
        self::assertStringNotContainsString('default@example.test', $messages[0]->toString());
    }

    private function mailFactory(): MailFactory
    {
        $app = $this->app;
        self::assertInstanceOf(Application::class, $app);

        $factory = $app->make(MailFactory::class);
        self::assertInstanceOf(MailFactory::class, $factory);

        return $factory;
    }

    /**
     * @return list<SentMessage>
     */
    private function sentMessages(): array
    {
        $app = $this->app;
        self::assertInstanceOf(Application::class, $app);

        $manager = $app->make('mail.manager');
        self::assertInstanceOf(MailManager::class, $manager);

        $mailer = $manager->mailer();
        self::assertInstanceOf(Mailer::class, $mailer);
        $transport = $mailer->getSymfonyTransport();
        self::assertInstanceOf(ArrayTransport::class, $transport);

        /** @var list<SentMessage> $messages */
        $messages = array_values($transport->messages()->all());

        return $messages;
    }
}
