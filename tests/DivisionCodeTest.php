<?php

namespace Dakalab\DivisionCode\Tests;

use Dakalab\DivisionCode\DivisionCode;

/**
 * Test class for Dakalab\DivisionCode\DivisionCode
 *
 * @coversDefaultClass \Dakalab\DivisionCode\DivisionCode
 * @runTestsInSeparateProcesses
 */
class DivisionCodeTest extends TestCase
{
    protected $divisionCode;

    protected function setUp()
    {
        parent::setUp();
        $this->divisionCode = new DivisionCode;
    }

    public function testGetCodesFile()
    {
        $res = $this->divisionCode->getCodesFile();
        $this->assertContains('codes.php', $res);
    }

    /**
     * @dataProvider getProvider
     */
    public function testGet($actual, $expected, $expectError): void
    {
        if ($expectError) {
            $this->expectException(\InvalidArgumentException::class);
        }
        $res = $this->divisionCode->get($actual);
        $this->assertEquals($expected, $res);
    }

    /**
     * @dataProvider getProvider
     */
    public function testGetWithoutSQLite($actual, $expected, $expectError): void
    {
        if ($expectError) {
            $this->expectException(\InvalidArgumentException::class);
        }
        $this->divisionCode->useSQLite(false);
        $res = $this->divisionCode->get($actual);
        $this->assertEquals($expected, $res);
        $this->divisionCode->useSQLite(true);
    }

    public function getProvider(): array
    {
        return [
            ['110000', '北京市', false],
            ['', '', true],
        ];
    }

    /**
     * @dataProvider getAddressProvider
     */
    public function testGetAddress($actual, $expected): void
    {
        $res = $this->divisionCode->getAddress($actual);
        $this->assertEquals($expected, $res);
    }

    public function getAddressProvider(): array
    {
        return [
            ['110000', '北京市'],
            ['445281', '广东省揭阳市普宁市'],
            ['440100', '广东省广州市'],
        ];
    }
}
