--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/MaximumNumber/Three/phpunit.xml';

require_once __DIR__ . '/../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: test/EndToEnd/MaximumNumber/Three/phpunit.xml
Random Seed:   %s

.........                                                           9 / 9 (100%)

Detected 8 tests that took longer than expected.

1,0%s ms (125 ms) Ergebnis\PHPUnit\SlowTestDetector\Test\EndToEnd\MaximumNumber\Three\SleeperTest::testSleeperSleepsOneSecond
  5%s ms (125 ms) Ergebnis\PHPUnit\SlowTestDetector\Test\EndToEnd\MaximumNumber\Three\SleeperTest::testSleeperSleepsWithSlowThresholdAnnotation#1
  4%s ms (125 ms) Ergebnis\PHPUnit\SlowTestDetector\Test\EndToEnd\MaximumNumber\Three\SleeperTest::testSleeperSleepsWithDocBlockWithSlowThresholdAnnotationWhereValueIsNotAnInt

There are 5 additional slow tests that are not listed here.

Time: %s, Memory: %s

OK (9 tests, 9 assertions)