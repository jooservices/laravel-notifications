<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Exceptions;

use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;

final class InvalidConfigurationException extends LaravelNotificationsException
{
    public static function emptyDefaultChannels(): self
    {
        return new self(
            'No notification channels were provided and default_channels is empty.',
        );
    }

    public static function invalidDefaultChannel(string $channel): self
    {
        return (new self(
            "Default notification channel '{$channel}' is not registered.",
        ))->withContext(['channel' => $channel]);
    }

    public function errorCode(): string
    {
        return 'laravel-notifications.config.invalid';
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
