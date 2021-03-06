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

    public function testGetCodes()
    {
        $codes = $this->divisionCode->getCodes();
        $this->assertEquals('北京市', $codes['110000']);
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

    public function testCount()
    {
        $this->divisionCode->useSQLite(false);
        $c1 = $this->divisionCode->count();

        $this->divisionCode->useSQLite(true);
        $c2 = $this->divisionCode->count();

        $this->assertEquals($c1, $c2);
    }

    /**
     * @dataProvider getSliceProvider
     */
    public function testGetSlice($useSQLite, $offset, $limit, $expected): void
    {
        $this->divisionCode->useSQLite($useSQLite);
        $res = $this->divisionCode->getSlice($offset, $limit);
        $this->assertEquals($expected, count($res));
    }

    public function getSliceProvider(): array
    {
        return [
            [true, 0, 1, 1],
            [true, 100, 200, 200],
            [true, 1000000, 10, 0],
            [false, 0, 1, 1],
            [false, 100, 200, 200],
            [false, 1000000, 10, 0],
        ];
    }

    /**
     * @dataProvider getAllProvincesProvider
     */
    public function testGetAllProvinces($useSQLite, $includeGAT, $expected): void
    {
        $this->divisionCode->useSQLite($useSQLite);
        $provinces = $this->divisionCode->getAllProvinces($includeGAT);
        $this->assertEquals($expected, count($provinces));
    }

    public function getAllProvincesProvider(): array
    {
        return [
            [true, false, 31],
            [true, true, 34],
            [false, false, 31],
            [false, true, 34],
        ];
    }

    /**
     * @dataProvider getCitiesInProvinceProvider
     */
    public function testGetCitiesInProvince($useSQLite, $province, $expected): void
    {
        $this->divisionCode->useSQLite($useSQLite);
        $cities = $this->divisionCode->getCitiesInProvince($province);
        $this->assertEquals($expected, count($cities));
    }

    public function getCitiesInProvinceProvider(): array
    {
        return [
            [true, '110000', 16],
            [true, '710000', 0],
            [true, '630000', 8],
            [true, '150000', 12],
            [true, '440000', 21],
            [true, '445200', 0], // not a province
            [false, '110000', 16],
            [false, '710000', 0],
            [false, '630000', 8],
            [false, '150000', 12],
            [false, '440000', 21],
            [false, '445200', 0], // not a province
        ];
    }

    /**
     * @dataProvider getCountiesInCityProvider
     */
    public function testGetCountiesInCity($useSQLite, $city, $expected): void
    {
        $this->divisionCode->useSQLite($useSQLite);
        $counties = $this->divisionCode->getCountiesInCity($city);
        $this->assertEquals($expected, count($counties));
    }

    public function getCountiesInCityProvider(): array
    {
        return [
            [true, '110000', 0],
            [true, '445200', 5],
            [true, '150100', 9],
            [true, '630100', 7],
            [true, '110101', 0], // not a city
            [false, '110000', 0],
            [false, '445200', 5],
            [false, '150100', 9],
            [false, '630100', 7],
            [false, '110101', 0], // not a city
        ];
    }

    /**
     * @dataProvider getProvinceCodeByNameProvider
     */
    public function testGetProvinceCodeByName($useSQLite, $name, $expected): void
    {
        $this->divisionCode->useSQLite($useSQLite);
        $res = $this->divisionCode->getProvinceCodeByName($name);
        $this->assertEquals($expected, $res);
    }

    public function getProvinceCodeByNameProvider(): array
    {
        return [
            [true, '北京市', '110000'],
            [true, '', ''],
            [true, '未知的地方', ''],
            [false, '北京市', '110000'],
            [false, '', ''],
            [false, '未知的地方', ''],
        ];
    }

    /**
     * @dataProvider getCitiesByProvinceNameProvider
     */
    public function testgetCitiesByProvinceName($useSQLite, $province, $expected): void
    {
        $this->divisionCode->useSQLite($useSQLite);
        $cities = $this->divisionCode->getCitiesByProvinceName($province);
        $this->assertEquals($expected, count($cities));
    }

    public function getCitiesByProvinceNameProvider(): array
    {
        return [
            [true, '北京市', 16],
            [true, '台湾省', 0],
            [true, '广东省', 21],
            [true, '广州市', 0], // not a province
            [false, '北京市', 16],
            [false, '台湾省', 0],
            [false, '广东省', 21],
            [false, '广州市', 0], // not a province
        ];
    }
}
