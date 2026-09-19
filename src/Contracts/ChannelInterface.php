<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Contracts;

use JOOservices\LaravelNotifications\Message;
use JOOservices\LaravelNotifications\SendResult;

interface ChannelInterface
{
    public function name(): string;

    public function send(Message $message): SendResult;
}
