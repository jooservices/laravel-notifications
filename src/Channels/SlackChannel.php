<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Channels;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Client\HttpClient;
use JOOservices\Client\Response\Response;
use JOOservices\LaravelNotifications\Contracts\ChannelInterface;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\SendResult;
use JOOservices\LaravelNotifications\Support\MessageFormatter;
use Throwable;

final class SlackChannel implements ChannelInterface
{
    public function __construct(
        private readonly string $webhookUrl,
        private readonly float $timeout = 5.0,
        private readonly ?HttpClient $client = null,
        private readonly MessageFormatter $formatter = new MessageFormatter(),
    ) {
    }

    public function name(): string
    {
        return 'slack';
    }

    public function send(Message $message): SendResult
    {
        if ($this->webhookUrl === '') {
            return SendResult::skipped($this->name(), 'SLACK webhook URL missing');
        }

        try {
            $client = $this->client ?? ClientBuilder::create()
                ->withTimeout($this->timeout)
                ->build();

            $prepared = $client->requestBuilder()
                ->post($this->webhookUrl)
                ->withJson([
                    'text' => $this->formatter->formatPlain($message),
                ])
                ->build();

            $psr = $client->sendRequest($prepared->toPsr());
            $response = Response::from($psr);

            if (! $response->successful()) {
                return SendResult::failed(
                    $this->name(),
                    'Slack webhook returned HTTP ' . $response->status(),
                );
            }

            return SendResult::sent($this->name());
        } catch (Throwable $exception) {
            return SendResult::failed($this->name(), $exception->getMessage());
        }
    }
}
