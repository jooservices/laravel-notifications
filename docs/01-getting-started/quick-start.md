# Quick start

```php
use JOOservices\LaravelNotifications\Facades\Notification;
use JOOservices\LaravelNotifications\Message;

$report = Notification::send(
    Message::make('Alert', view('ops.alert', $data)->render()),
    ['telegram'],
);
```

Missing Telegram credentials skip the channel without throwing. Inspect `$report->forChannel('telegram')`.
