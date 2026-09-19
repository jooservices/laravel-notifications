<?php

declare(strict_types=1);

namespace JOOservices\LaravelNotifications\Tests\Unit;

use JOOservices\LaravelNotifications\SendReport;
use JOOservices\LaravelNotifications\SendResult;
use JOOservices\LaravelNotifications\Tests\TestCase;

final class SendReportTest extends TestCase
{
    public function testAggregatesFailuresAndEmptyReport(): void
    {
        $empty = SendReport::fromResults([]);
        self::assertFalse($empty->allSuccessful());
        self::assertFalse($empty->hasFailures());
        self::assertSame([], $empty->failures());
        self::assertNull($empty->forChannel('telegram'));

        $report = SendReport::fromResults([
            SendResult::sent('telegram'),
            SendResult::failed('slack', 'boom'),
            SendResult::skipped('mail', 'missing'),
        ]);

        self::assertTrue($report->hasFailures());
        self::assertFalse($report->allSuccessful());
        self::assertCount(1, $report->failures());
        self::assertSame('slack', $report->failures()[0]->channel);
    }
}
