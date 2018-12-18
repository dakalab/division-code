<?php

namespace Dakalab\DivisionCode;

class DivisionCode
{
    public static $codes = [];

    public function __construct()
    {
        $this->loadCodes();
    }

    /**
     * Get the codes file path
     *
     * @return string
     */
    public function getCodesFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'codes.php';
    }

    /**
     * Load codes
     *
     * @return void
     */
    protected function loadCodes()
    {
        self::$codes = require $this->getCodesFile();
    }

    /**
     * Get codes
     *
     * @return array
     */
    public function getCodes(): array
    {
        return self::$codes;
    }

    /**
     * Validate the code, should be a 6-digit number
     *
     * @param  string|int $code
     * @return bool
     */
    protected function validate($code): bool
    {
        return preg_match('/^\d{6}$/', $code);
    }

    /**
     * Get local name by code
     *
     * @param  string|int                  $code
     * @throws \InvalidArgumentException
     * @return string
     */
    public function get($code = null): string
    {
        if (!$this->validate($code)) {
            throw new \InvalidArgumentException('Invalid code');
        }

        return isset(self::$codes[$code]) ? self::$codes[$code] : '';
    }

    /**
     * Get the province by code
     *
     * @param  string|int $code
     * @return string
     */
    public function getProvince($code): string
    {
        $provinceCode = substr($code, 0, 2) . '0000';

        return $this->get($provinceCode);
    }

    /**
     * Get the city by code
     *
     * @param  string|int $code
     * @return string
     */
    public function getCity($code): string
    {
        $provinceCode = substr($code, 0, 2) . '0000';
        $cityCode = substr($code, 0, 4) . '00';
        if ($provinceCode != $cityCode) {
            return $this->get($cityCode);
        }

        return '';
    }

    /**
     * Get the county by code
     *
     * @param  string|int $code
     * @return string
     */
    public function getCounty($code): string
    {
        if (substr($code, 4, 2) != '00') {
            return $this->get($code);
        }

        return '';
    }

    /**
     * Get the address by code
     *
     * @param  string|int $code
     * @return string
     */
    public function getAddress($code): string
    {
        return $this->getProvince($code) . $this->getCity($code) . $this->getCounty($code);
    }
}
