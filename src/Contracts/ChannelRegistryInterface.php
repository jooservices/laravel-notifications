<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Contracts;

use JOOservices\LaravelNotifications\Exceptions\ChannelNotRegisteredException;

interface ChannelRegistryInterface
{
    public function register(ChannelInterface $channel): void;

    public function has(string $name): bool;

    /**
     * @throws ChannelNotRegisteredException
     */
    public function get(string $name): ChannelInterface;

    /**
     * @return list<string>
     */
    public function names(): array;
}
