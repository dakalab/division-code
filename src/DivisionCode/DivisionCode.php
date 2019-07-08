<?php

namespace Dakalab\DivisionCode;

class DivisionCode
{
    public static $codes = [];

    protected $disableSQLite = false;

    protected $db;

    public function __construct()
    {
        if ($this->supportSQLite()) {
            $this->db = new \SQLite3($this->getCodesDB());
        } else {
            $this->loadCodes();
        }
    }

    public function __destruct()
    {
        if ($this->supportSQLite() && !empty($this->db)) {
            $this->db->close();
        }
    }

    /**
     * Turn on or turn off using SQLite
     *
     * @param  bool           $v
     * @return DivisionCode
     */
    public function useSQLite($v = true)
    {
        $this->disableSQLite = !$v;

        if ($this->disableSQLite && empty(self::$codes)) {
            $this->loadCodes();
        }

        return $this;
    }

    /**
     * Check if SQLite3 is supported
     *
     * @return bool
     */
    public function supportSQLite(): bool
    {
        return class_exists('SQLite3', false) && !$this->disableSQLite;
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
     * Get the codes db path
     *
     * @return string
     */
    public function getCodesDB(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'codes.db';
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
     * Get all the codes
     *
     * @return array
     */
    public function getCodes(): array
    {
        if (empty(self::$codes)) {
            $this->loadCodes();
        }
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

        if ($this->supportSQLite()) {
            $sql = sprintf("SELECT name FROM division_codes WHERE code = '%s'", $code);

            return $this->db->querySingle($sql);
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
