# Application usage

```php
$body = view('mail.ops.source_unhealthy', [
    'source' => $source->slug,
    'reason' => $reason,
])->render();

$report = Notification::send(
    Message::make('Source unhealthy', $body)
        ->withContext(['source_slug' => $source->slug])
        ->recipients(['ops@example.test']),
    ['telegram', 'mail'],
);

foreach ($report->failures() as $failure) {
    logger()->warning('notification.failed', [
        'channel' => $failure->channel,
        'reason' => $failure->reason,
    ]);
}
```
