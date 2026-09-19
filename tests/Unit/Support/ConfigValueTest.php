<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit\Support;

use JOOservices\LaravelNotifications\Support\ConfigValue;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class ConfigValueTest extends TestCase
{
    public function testStringAndFloatConversions(): void
    {
        self::assertSame('abc', ConfigValue::string('abc'));
        self::assertSame('12', ConfigValue::string(12));
        self::assertSame('1.5', ConfigValue::string(1.5));
        self::assertSame('fallback', ConfigValue::string(null, 'fallback'));
        self::assertSame('fallback', ConfigValue::string(['x'], 'fallback'));

        self::assertSame(5.0, ConfigValue::float(5));
        self::assertSame(2.5, ConfigValue::float('2.5'));
        self::assertSame(1.0, ConfigValue::float('nope', 1.0));
        self::assertSame(0.0, ConfigValue::float(null));
    }
}
