<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Support;

use JOOservices\LaravelNotifications\Message;

final class MessageFormatter
{
    public function formatPlain(Message $message): string
    {
        $lines = [
            $message->subject,
            $message->plainText(),
        ];

        foreach ($message->context as $key => $value) {
            $lines[] = sprintf('%s: %s', $key, $value === null ? 'null' : (string) $value);
        }

        return implode("\n", $lines);
    }
}
