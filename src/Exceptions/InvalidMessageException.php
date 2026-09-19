<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Exceptions;

use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;

final class InvalidMessageException extends LaravelNotificationsException
{
    public static function emptySubject(): self
    {
        return new self('Notification message subject must not be empty.');
    }

    public static function emptyBody(): self
    {
        return new self('Notification message requires a non-empty body or htmlBody.');
    }

    public static function invalidContext(): self
    {
        return new self('Notification message context values must be scalar or null.');
    }

    public function errorCode(): string
    {
        return 'laravel-notifications.message.invalid';
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
