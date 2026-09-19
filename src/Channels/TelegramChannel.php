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

final class TelegramChannel implements ChannelInterface
{
    public function __construct(
        private readonly string $botToken,
        private readonly string $chatId,
        private readonly string $apiBase = 'https://api.telegram.org',
        private readonly float $timeout = 5.0,
        private readonly ?HttpClient $client = null,
        private readonly MessageFormatter $formatter = new MessageFormatter(),
    ) {
    }

    public function name(): string
    {
        return 'telegram';
    }

    public function send(Message $message): SendResult
    {
        if ($this->botToken === '' || $this->chatId === '') {
            return SendResult::skipped($this->name(), 'TELEGRAM bot token or chat id missing');
        }

        try {
            $client = $this->client ?? ClientBuilder::create()
                ->withBaseUri(rtrim($this->apiBase, '/') . '/')
                ->withTimeout($this->timeout)
                ->build();

            $prepared = $client->requestBuilder()
                ->post('bot' . $this->botToken . '/sendMessage')
                ->withJson([
                    'chat_id' => $this->chatId,
                    'text' => $this->formatter->formatPlain($message),
                    'disable_web_page_preview' => true,
                ])
                ->build();

            $psr = $client->sendRequest($prepared->toPsr());
            $response = Response::from($psr);

            if (! $response->successful()) {
                return SendResult::failed(
                    $this->name(),
                    'Telegram API returned HTTP ' . $response->status(),
                );
            }

            return SendResult::sent($this->name());
        } catch (Throwable $exception) {
            return SendResult::failed($this->name(), $exception->getMessage());
        }
    }
}
