<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/phpunit-slow-test-detector
 */

namespace Ergebnis\PHPUnit\SlowTestDetector\Subscriber;

use Ergebnis\PHPUnit\SlowTestDetector\Collector;
use Ergebnis\PHPUnit\SlowTestDetector\Reporter;
use PHPUnit\Event;

/**
 * @internal
 */
final class TestRunnerExecutionFinishedSubscriber implements Event\TestRunner\ExecutionFinishedSubscriber
{
    public function __construct(
        private readonly Collector\Collector $collector,
        private readonly Reporter\Reporter $reporter,
    ) {
    }

    public function notify(Event\TestRunner\ExecutionFinished $event): void
    {
        $slowTests = $this->collector->collected();

        if ([] === $slowTests) {
            return;
        }

        $report = $this->reporter->report(...$slowTests);

        if ('' === $report) {
            return;
        }

        echo <<<TXT


{$report}
TXT;
    }
}
