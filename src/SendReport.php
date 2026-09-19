<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications;

use JOOservices\Dto\Core\Dto;

final class SendReport extends Dto
{
    /**
     * @param  list<SendResult>  $results
     */
    public function __construct(
        public readonly array $results,
    ) {
    }

    /**
     * @param  list<SendResult>  $results
     */
    public static function fromResults(array $results): self
    {
        return new self(array_values($results));
    }

    public function allSuccessful(): bool
    {
        foreach ($this->results as $result) {
            if ($result->isFailed()) {
                return false;
            }
        }

        return $this->results !== [];
    }

    public function hasFailures(): bool
    {
        foreach ($this->results as $result) {
            if ($result->isFailed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<SendResult>
     */
    public function failures(): array
    {
        return array_values(array_filter(
            $this->results,
            static fn(SendResult $result): bool => $result->isFailed(),
        ));
    }

    public function forChannel(string $channel): ?SendResult
    {
        foreach ($this->results as $result) {
            if ($result->channel === $channel) {
                return $result;
            }
        }

        return null;
    }
}
