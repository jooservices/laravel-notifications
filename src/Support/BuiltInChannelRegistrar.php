<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Support;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use JOOservices\LaravelNotifications\ChannelRegistry;
use JOOservices\LaravelNotifications\Channels\MailChannel;
use JOOservices\LaravelNotifications\Channels\SlackChannel;
use JOOservices\LaravelNotifications\Channels\TelegramChannel;

final class BuiltInChannelRegistrar
{
    public function __construct(
        private readonly ConfigRepository $config,
        private readonly MailFactory $mail,
    ) {
    }

    public function register(ChannelRegistry $registry): void
    {
        $this->registerTelegram($registry);
        $this->registerSlack($registry);
        $this->registerMail($registry);
    }

    private function registerTelegram(ChannelRegistry $registry): void
    {
        /** @var array<string, mixed> $telegram */
        $telegram = $this->config->get('laravel-notifications.channels.telegram', []);
        if (! is_array($telegram)) {
            $telegram = [];
        }

        $registry->register(new TelegramChannel(
            botToken: ConfigValue::string($telegram['bot_token'] ?? ''),
            chatId: ConfigValue::string($telegram['chat_id'] ?? ''),
            apiBase: ConfigValue::string(
                $telegram['api_base'] ?? 'https://api.telegram.org',
                'https://api.telegram.org',
            ),
            timeout: ConfigValue::float($telegram['timeout'] ?? 5, 5.0),
        ));
    }

    private function registerSlack(ChannelRegistry $registry): void
    {
        /** @var array<string, mixed> $slack */
        $slack = $this->config->get('laravel-notifications.channels.slack', []);
        if (! is_array($slack)) {
            $slack = [];
        }

        $registry->register(new SlackChannel(
            webhookUrl: ConfigValue::string($slack['webhook_url'] ?? ''),
            timeout: ConfigValue::float($slack['timeout'] ?? 5, 5.0),
        ));
    }

    private function registerMail(ChannelRegistry $registry): void
    {
        /** @var array<string, mixed> $mail */
        $mail = $this->config->get('laravel-notifications.channels.mail', []);
        if (! is_array($mail)) {
            $mail = [];
        }

        $from = null;
        $fromConfig = $mail['from'] ?? null;
        if (is_array($fromConfig) && ($fromConfig['address'] ?? '') !== '') {
            $from = [
                'address' => ConfigValue::string($fromConfig['address'] ?? ''),
                'name' => isset($fromConfig['name']) ? ConfigValue::string($fromConfig['name']) : null,
            ];
        }

        $defaultTo = [];
        foreach ((array) ($mail['to'] ?? []) as $email) {
            if (is_string($email) && $email !== '') {
                $defaultTo[] = $email;
            }
        }

        $registry->register(new MailChannel(
            mail: $this->mail,
            defaultTo: $defaultTo,
            from: $from,
            mailerName: isset($mail['mailer']) && is_string($mail['mailer']) && $mail['mailer'] !== ''
                ? $mail['mailer']
                : null,
        ));
    }
}
