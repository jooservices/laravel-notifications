<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications;

use JOOservices\Dto\Core\Dto;
use JOOservices\LaravelNotifications\Exceptions\InvalidMessageException;

/**
 * Pre-rendered notification content. The application owns templates;
 * this package only transports already-rendered strings.
 */
final class Message extends Dto
{
    /**
     * @param  array<string, string|int|float|bool|null>  $context
     * @param  list<string>|null  $to
     *
     * @throws InvalidMessageException
     */
    public function __construct(
        public readonly string $subject,
        public readonly string $body,
        public readonly ?string $htmlBody = null,
        public readonly array $context = [],
        public readonly ?array $to = null,
    ) {
        if (trim($this->subject) === '') {
            throw InvalidMessageException::emptySubject();
        }

        if (trim($this->body) === '' && ($this->htmlBody === null || trim($this->htmlBody) === '')) {
            throw InvalidMessageException::emptyBody();
        }

        foreach ($this->context as $key => $value) {
            if (! is_string($key) || (! is_scalar($value) && $value !== null)) {
                throw InvalidMessageException::invalidContext();
            }
        }
    }

    /**
     * @param  array<string, string|int|float|bool|null>  $context
     * @param  list<string>|null  $to
     *
     * @throws InvalidMessageException
     */
    public static function make(
        string $subject,
        string $body,
        ?string $htmlBody = null,
        array $context = [],
        ?array $to = null,
    ): self {
        return new self($subject, $body, $htmlBody, $context, $to);
    }

    /**
     * @throws InvalidMessageException
     */
    public function html(string $htmlBody): self
    {
        return new self($this->subject, $this->body, $htmlBody, $this->context, $this->to);
    }

    /**
     * @param  array<string, string|int|float|bool|null>  $context
     *
     * @throws InvalidMessageException
     */
    public function withContext(array $context): self
    {
        return new self($this->subject, $this->body, $this->htmlBody, $context, $this->to);
    }

    /**
     * @param  list<string>  $recipients
     *
     * @throws InvalidMessageException
     */
    public function recipients(array $recipients): self
    {
        return new self($this->subject, $this->body, $this->htmlBody, $this->context, array_values($recipients));
    }

    public function plainText(): string
    {
        return $this->body !== '' ? $this->body : strip_tags((string) $this->htmlBody);
    }
}
