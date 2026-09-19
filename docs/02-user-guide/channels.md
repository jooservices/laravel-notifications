# Channels

## Telegram (`telegram`)

Posts to `{api_base}/bot{token}/sendMessage` as JSON. Text is subject + body + scalar context lines.

Skip when `bot_token` or `chat_id` is empty.

## Slack (`slack`)

POSTs JSON `{"text": "..."}` to the incoming webhook URL.

Skip when `webhook_url` is empty.

## Mail (`mail`)

Uses Laravel `Mailer::raw()` or `send(['html' => ..., 'text' => ...])` with pre-rendered strings.

Skip when neither message `to` nor config `channels.mail.to` provides recipients.
