<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications;

use JOOservices\LaravelNotifications\Contracts\ChannelInterface;
use JOOservices\LaravelNotifications\Contracts\ChannelRegistryInterface;
use JOOservices\LaravelNotifications\Exceptions\ChannelNotRegisteredException;

final class ChannelRegistry implements ChannelRegistryInterface
{
    /** @var array<string, ChannelInterface> */
    private array $channels = [];

    public function register(ChannelInterface $channel): void
    {
        $this->channels[$channel->name()] = $channel;
    }

    public function has(string $name): bool
    {
        return isset($this->channels[$name]);
    }

    public function get(string $name): ChannelInterface
    {
        if (! isset($this->channels[$name])) {
            throw ChannelNotRegisteredException::forChannel($name);
        }

        return $this->channels[$name];
    }

    public function names(): array
    {
        return array_keys($this->channels);
    }
}
