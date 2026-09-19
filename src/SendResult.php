<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications;

use JOOservices\Dto\Core\Dto;

final class SendResult extends Dto
{
    public const STATUS_SENT = 'sent';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_FAILED = 'failed';

    public function __construct(
        public readonly string $channel,
        public readonly string $status,
        public readonly ?string $reason = null,
    ) {
    }

    public static function sent(string $channel): self
    {
        return new self($channel, self::STATUS_SENT);
    }

    public static function skipped(string $channel, string $reason): self
    {
        return new self($channel, self::STATUS_SKIPPED, $reason);
    }

    public static function failed(string $channel, string $reason): self
    {
        return new self($channel, self::STATUS_FAILED, $reason);
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isSkipped(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
