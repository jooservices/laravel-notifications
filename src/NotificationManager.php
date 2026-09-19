<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications;

use JOOservices\LaravelNotifications\Contracts\ChannelRegistryInterface;
use JOOservices\LaravelNotifications\Exceptions\ChannelNotRegisteredException;
use JOOservices\LaravelNotifications\Exceptions\InvalidConfigurationException;

final class NotificationManager
{
    /**
     * @param  list<string>  $defaultChannels
     */
    public function __construct(
        private readonly ChannelRegistryInterface $registry,
        private readonly array $defaultChannels = [],
    ) {
    }

    /**
     * @param  list<string>|null  $channels
     *
     * @throws ChannelNotRegisteredException
     * @throws InvalidConfigurationException
     */
    public function send(Message $message, ?array $channels = null): SendReport
    {
        $targets = $channels ?? $this->defaultChannels;

        if ($targets === []) {
            throw InvalidConfigurationException::emptyDefaultChannels();
        }

        $results = [];

        foreach ($targets as $name) {
            $results[] = $this->registry->get($name)->send($message);
        }

        return SendReport::fromResults($results);
    }

    public function registry(): ChannelRegistryInterface
    {
        return $this->registry;
    }
}
