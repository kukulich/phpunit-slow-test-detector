<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/phpunit-slow-test-collector
 */

namespace Ergebnis\PHPUnit\SlowTestCollector\Test\Unit\Subscriber;

use Ergebnis\PHPUnit\SlowTestCollector\SlowTest;
use Ergebnis\PHPUnit\SlowTestCollector\SlowTestCollector;
use Ergebnis\PHPUnit\SlowTestCollector\Subscriber\TestPreparedSubscriber;
use Ergebnis\Test\Util;
use PHPUnit\Event;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\PHPUnit\SlowTestCollector\Subscriber\TestPreparedSubscriber
 *
 * @uses \Ergebnis\PHPUnit\SlowTestCollector\SlowTest
 * @uses \Ergebnis\PHPUnit\SlowTestCollector\SlowTestCollector
 */
final class TestPreparedSubscriberTest extends Framework\TestCase
{
    use Util\Helper;

    public function testNotifyCollectsPreparedTest(): void
    {
        $faker = self::faker();

        $maximumDuration = Event\Telemetry\Duration::fromSeconds($faker->numberBetween(
            5,
            10
        ));

        $preparedTime = Event\Telemetry\HRTime::fromSecondsAndNanoseconds(
            $faker->numberBetween(),
            0
        );

        $preparedTest = self::createTest('test');

        $preparedTestEvent = new Event\Test\Prepared(
            self::createTelemetryInfo($preparedTime),
            $preparedTest
        );

        $passedTime = Event\Telemetry\HRTime::fromSecondsAndNanoseconds(
            $preparedTime->seconds() + $maximumDuration->seconds() + 1,
            0
        );

        $passedTest = clone $preparedTest;

        $slowTestCollector = new SlowTestCollector($maximumDuration);

        $subscriber = new TestPreparedSubscriber($slowTestCollector);

        $subscriber->notify($preparedTestEvent);

        self::assertSame([], $slowTestCollector->slowTests());

        $slowTestCollector->testPassed(
            $passedTest,
            $passedTime
        );

        $expected = [
            SlowTest::fromTestAndDuration(
                $passedTest,
                $passedTime->duration($preparedTime)
            ),
        ];

        self::assertEquals($expected, $slowTestCollector->slowTests());
    }

    private static function createTelemetryInfo(Event\Telemetry\HRTime $time): Event\Telemetry\Info
    {
        $faker = self::faker();

        return new Event\Telemetry\Info(
            new Event\Telemetry\Snapshot(
                $time,
                Event\Telemetry\MemoryUsage::fromBytes($faker->numberBetween()),
                Event\Telemetry\MemoryUsage::fromBytes($faker->numberBetween())
            ),
            Event\Telemetry\Duration::fromSeconds($faker->numberBetween()),
            Event\Telemetry\MemoryUsage::fromBytes($faker->numberBetween()),
            Event\Telemetry\Duration::fromSeconds($faker->numberBetween()),
            Event\Telemetry\MemoryUsage::fromBytes($faker->numberBetween()),
        );
    }

    private static function createTest(string $methodName): Event\Code\Test
    {
        $faker = self::faker();

        $methodNameWithDataSet = \sprintf(
            '%s with data set #%d',
            $methodName,
            $faker->numberBetween()
        );

        if ($faker->boolean) {
            $methodNameWithDataSet = $methodName;
        }

        return new Event\Code\Test(
            self::class,
            $methodName,
            $methodNameWithDataSet
        );
    }
}
