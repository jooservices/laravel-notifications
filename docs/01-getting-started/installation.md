# Installation

```bash
composer require jooservices/laravel-notifications:^1.0
```

Publish config:

```bash
php artisan vendor:publish --provider="JOOservices\LaravelNotifications\LaravelNotificationsServiceProvider" --tag="config"
```

The service provider is auto-discovered. The `Notification` facade alias is registered via `composer.json` `extra.laravel.aliases`.
