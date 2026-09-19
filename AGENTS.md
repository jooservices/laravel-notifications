# jooservices/laravel-notifications

This file adds project-only rules.

- PHP `^8.5`, Laravel `^12|^13` (illuminate/support, illuminate/mail, illuminate/contracts)
- Runtime deps: `jooservices/client` ^4, `jooservices/dto` ^3.2, `jooservices/exceptions` ^4
- Scope: **send-only** transport (Telegram, Slack, mail). No queue, templates, debounce, or Log channel
- Application renders templates and passes pre-rendered `Message` subject/body/(optional) htmlBody
- Missing channel credentials → skip (fail-open); network/API errors → failed result; other channels continue
- All PHP tooling via Docker (`php:8.5-cli-bookworm` + curl)
- CI on GitHub-hosted `ubuntu-latest` runners
- Lints at **max** with **no ignore**: PHPStan max, full PSR-12 PHPCS, full PHPMD rulesets, Pint `per`
- First public line: **v1.0.0**
