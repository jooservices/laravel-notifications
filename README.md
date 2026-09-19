# jooservices/laravel-notifications

[![CI](https://github.com/jooservices/laravel-notifications/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/jooservices/laravel-notifications/actions/workflows/ci.yml)
[![Coverage (develop)](https://codecov.io/gh/jooservices/laravel-notifications/branch/develop/graph/badge.svg)](https://codecov.io/gh/jooservices/laravel-notifications/branch/develop)
[![Quality Gate (master)](https://sonarcloud.io/api/project_badges/measure?project=jooservices_laravel-notifications&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=jooservices_laravel-notifications)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/jooservices/laravel-notifications/badge)](https://securityscorecards.dev/viewer/?uri=github.com/jooservices/laravel-notifications)
[![PHP Version](https://img.shields.io/badge/PHP-8.5%2B-blue.svg)](https://www.php.net/)
[![GitHub Release](https://img.shields.io/github/v/release/jooservices/laravel-notifications?display_name=tag)](https://github.com/jooservices/laravel-notifications/releases)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/jooservices/laravel-notifications)](https://packagist.org/packages/jooservices/laravel-notifications)
[![Total Downloads](https://img.shields.io/packagist/dt/jooservices/laravel-notifications)](https://packagist.org/packages/jooservices/laravel-notifications)

**JOOservices Laravel Notifications** is a send-only Laravel notification transport for Telegram, Slack, and email. The application owns templates, queueing, debounce, and logging. This package transports **pre-rendered** subject/body strings through channel adapters and returns a structured `SendReport`.

Composer package: `jooservices/laravel-notifications` — current line: **v1.0.0**.

## Features

- Telegram Bot API (`sendMessage`) via `jooservices/client`
- Slack incoming webhooks via `jooservices/client`
- Mail via Laravel `Mailer` (`raw` / HTML send) — no package Mailables or Blade views
- Immutable `Message` DTO (`jooservices/dto`) with optional HTML body, scalar context, and recipients
- Per-channel `SendResult` and aggregate `SendReport`
- Missing credentials → **skip** (fail-open); HTTP/API errors → **failed**; other channels continue
- Context-aware exceptions (`jooservices/exceptions`)
- Auto-discovered service provider and `Notification` facade

## Non-goals

- Queue / `ShouldQueue` helpers
- Templates / Blade / package Mailables / markdown mail
- Log / Discord / SMS channels
- Debounce / rate-limit

## Requirements

- PHP `^8.5`
- Laravel / Illuminate `^12|^13`
- Extensions: `curl` (for Telegram/Slack HTTP)

## Installation

```bash
composer require jooservices/laravel-notifications:^1.0
```

## Publish config

```bash
php artisan vendor:publish --provider="JOOservices\LaravelNotifications\LaravelNotificationsServiceProvider" --tag="config"
```

## Environment

| Variable | Purpose |
| --- | --- |
| `NOTIFICATION_DEFAULT_CHANNELS` | Comma-separated defaults (e.g. `telegram,slack,mail`) |
| `NOTIFICATION_TELEGRAM_BOT_TOKEN` | Telegram bot token (also accepts `TELEGRAM_BOT_TOKEN`) |
| `NOTIFICATION_TELEGRAM_CHAT_ID` | Telegram chat id (also accepts `TELEGRAM_CHAT_ID`) |
| `NOTIFICATION_TELEGRAM_API_BASE` | API base (default `https://api.telegram.org`) |
| `NOTIFICATION_TELEGRAM_TIMEOUT` | Seconds (default `5`) |
| `NOTIFICATION_SLACK_WEBHOOK_URL` | Slack incoming webhook URL |
| `NOTIFICATION_SLACK_TIMEOUT` | Seconds (default `5`) |
| `NOTIFICATION_MAIL_MAILER` | Optional named mailer |
| `NOTIFICATION_MAIL_TO` | Comma-separated default recipients |
| `NOTIFICATION_MAIL_FROM_ADDRESS` / `NOTIFICATION_MAIL_FROM_NAME` | Optional from override |

## Quick start

```php
use JOOservices\LaravelNotifications\Facades\Notification;
use JOOservices\LaravelNotifications\Message;

$body = view('ops.crawl_alert', $data)->render(); // app owns templates

$report = Notification::send(
    Message::make(subject: 'Source unhealthy: javdb', body: $body)
        ->html($htmlBody) // optional; mail only
        ->withContext(['source_slug' => 'javdb']),
    channels: ['telegram', 'slack', 'mail'],
);

if ($report->hasFailures()) {
    // app may log from SendReport — package does not
}
```

Omit `channels` to use `NOTIFICATION_DEFAULT_CHANNELS` / config `default_channels`.

## Local development

```bash
make build
make install
make lint
make test
```

All PHP tooling runs in Docker (`php:8.5-cli-bookworm`). See [WORKFLOWS.md](WORKFLOWS.md) for CI.

## Documentation

- [docs/README.md](docs/README.md) — documentation map
- [CHANGELOG.md](CHANGELOG.md)
- [CONTRIBUTING.md](CONTRIBUTING.md)
- [SECURITY.md](SECURITY.md)

## License

MIT — see [LICENSE](LICENSE).
