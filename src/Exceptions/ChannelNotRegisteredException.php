<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Exceptions;

use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;

final class ChannelNotRegisteredException extends LaravelNotificationsException
{
    public static function forChannel(string $channel): self
    {
        return (new self(
            "Notification channel '{$channel}' is not registered.",
        ))->withContext(['channel' => $channel]);
    }

    public function errorCode(): string
    {
        return 'laravel-notifications.channel.not_registered';
    }

    public function logLevel(): string
    {
        return LogLevel::ERROR->value;
    }

    protected function copyWithContext(ExceptionContext $context): static
    {
        return new self($this->getMessage(), $this->getCode(), $this->getPrevious(), $context);
    }
}
