# Changelog

All notable changes to this package are documented in this file.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/);
versioning follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.0.0] - 2026-09-18

First stable release of the send-only notification transport.

### Added

- Send-only notification transport for Laravel 12/13
- Built-in channels: Telegram Bot API, Slack incoming webhook, and mail (pre-rendered strings)
- `Message`, `SendResult`, and `SendReport` DTOs via `jooservices/dto`
- `NotificationManager`, `ChannelRegistry`, and `Notification` facade
- Context-aware exceptions via `jooservices/exceptions`
- HTTP via `jooservices/client` for Telegram and Slack
- Publishable `config/laravel-notifications.php` with env-driven credentials
- Fail-open skip when credentials are missing; per-channel failure isolation
