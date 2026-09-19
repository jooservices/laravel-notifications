<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Support;

use JOOservices\LaravelNotifications\Contracts\ChannelInterface;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\SendResult;

final class FakeChannel implements ChannelInterface
{
    /** @var list<Message> */
    public array $sent = [];

    public function __construct(
        private readonly string $channelName,
        private readonly string $outcome = SendResult::STATUS_SENT,
        private readonly ?string $reason = null,
    ) {
    }

    public function name(): string
    {
        return $this->channelName;
    }

    public function send(Message $message): SendResult
    {
        $this->sent[] = $message;

        return match ($this->outcome) {
            SendResult::STATUS_SKIPPED => SendResult::skipped($this->channelName, $this->reason ?? 'skipped'),
            SendResult::STATUS_FAILED => SendResult::failed($this->channelName, $this->reason ?? 'failed'),
            default => SendResult::sent($this->channelName),
        };
    }
}
