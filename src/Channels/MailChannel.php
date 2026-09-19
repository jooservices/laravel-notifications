<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Channels;

use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Mail\Message as MailMessage;
use Illuminate\Support\HtmlString;
use JOOservices\LaravelNotifications\Contracts\ChannelInterface;
use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\SendResult;
use Throwable;

final class MailChannel implements ChannelInterface
{
    /**
     * @param  list<string>  $defaultTo
     * @param  array{address: string, name: string|null}|null  $from
     */
    public function __construct(
        private readonly MailFactory $mail,
        private readonly array $defaultTo = [],
        private readonly ?array $from = null,
        private readonly ?string $mailerName = null,
    ) {
    }

    public function name(): string
    {
        return 'mail';
    }

    public function send(Message $message): SendResult
    {
        $recipients = $message->to ?? $this->defaultTo;
        $recipients = array_values(array_filter($recipients, static fn(string $email): bool => $email !== ''));

        if ($recipients === []) {
            return SendResult::skipped($this->name(), 'mail recipients missing');
        }

        try {
            $mailer = $this->mail->mailer($this->mailerName);

            $callback = function (MailMessage $mail) use ($message, $recipients): void {
                $mail->to($recipients)->subject($message->subject);

                if ($this->from !== null && ($this->from['address'] ?? '') !== '') {
                    $mail->from($this->from['address'], $this->from['name'] ?? null);
                }
            };

            if ($message->htmlBody !== null && trim($message->htmlBody) !== '') {
                $mailer->send([
                    'html' => new HtmlString($message->htmlBody),
                    'text' => new HtmlString($message->plainText()),
                ], [], $callback);

                return SendResult::sent($this->name());
            }

            $mailer->raw($message->plainText(), $callback);

            return SendResult::sent($this->name());
        } catch (Throwable $exception) {
            // Illuminate Mailer does not document thrown exceptions; keep fail-open.
            return SendResult::failed($this->name(), $exception->getMessage());
        }
    }
}
