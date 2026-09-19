# Repository structure

```text
src/
  Channels/          TelegramChannel, SlackChannel, MailChannel
  Contracts/         ChannelInterface, ChannelRegistryInterface
  Exceptions/        context-aware package exceptions
  Facades/           Notification
  Support/           MessageFormatter
  ChannelRegistry.php
  Message.php
  NotificationManager.php
  SendReport.php
  SendResult.php
  LaravelNotificationsServiceProvider.php
config/laravel-notifications.php
tests/Unit|Feature
```
