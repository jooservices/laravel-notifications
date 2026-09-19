# Project overview

`jooservices/laravel-notifications` is a **send-only** transport layer.

```text
App renders template → Message (subject + body) → NotificationManager
  → ChannelRegistry → Telegram / Slack / Mail adapters → SendReport
```

## Boundaries

| In package | Out of package (app owns) |
| --- | --- |
| Channel adapters | Blade / Markdown templates |
| HTTP (Telegram/Slack via `jooservices/client`) | Queue / debounce |
| Mail raw/HTML string send | Logging / alerting policy |
| Structured send results | Domain events |

## Dependencies

- `jooservices/dto` — `Message`, `SendResult`, `SendReport`
- `jooservices/client` — Telegram and Slack HTTP
- `jooservices/exceptions` — context-aware package exceptions
- `illuminate/mail` + `illuminate/support` — mail transport and container wiring
