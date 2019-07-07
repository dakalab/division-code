<?php

namespace Dakalab\DivisionCode\Tests;

use Dakalab\DivisionCode\Upgrader;

/**
 * Test class for Dakalab\DivisionCode\Upgrader
 *
 * @coversDefaultClass \Dakalab\DivisionCode\Upgrader
 * @runTestsInSeparateProcesses
 */
class UpgraderTest extends TestCase
{
    public function testUpgrade()
    {
        $upgrader = new Upgrader;
        $res = $upgrader->upgrade();
        $this->assertTrue($res);
    }
}
