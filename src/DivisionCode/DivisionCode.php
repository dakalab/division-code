<?php

namespace Dakalab\DivisionCode;

class DivisionCode
{
    /**
     * Static array to cache [code => name] of divisions, only used when sqlite is disabled
     *
     * @var array
     */
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

            return (string) $this->db->querySingle($sql);
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

    /**
     * Get the total number of codes
     *
     * @return int
     */
    public function count(): int
    {
        if ($this->supportSQLite()) {
            $sql = 'SELECT COUNT(*) FROM division_codes';

            return (int) $this->db->querySingle($sql);
        }

        return count(self::$codes);
    }

    /**
     * Get a slice of division codes
     *
     * @param  int     $offset
     * @param  int     $limit
     * @return array
     */
    public function getSlice($offset = 0, $limit = 1): array
    {
        if ($this->supportSQLite()) {
            $sql = 'SELECT code, name FROM division_codes LIMIT %d OFFSET %d';
            $res = $this->db->query(sprintf($sql, $limit, $offset));
            $arr = [];
            while ($row = $res->fetchArray()) {
                $arr[$row['code']] = $row['name'];
            }

            return $arr;
        }

        return array_slice(self::$codes, $offset, $limit, true);
    }

    /**
     * Get all the provinces
     *
     * @param  bool $includeGAT include Hong Kong, Macao and Taiwan? exclude them by default
     * @return array
     */
    public function getAllProvinces($includeGAT = false): array
    {
        if ($this->supportSQLite()) {
            $sql = 'SELECT code, name FROM division_codes WHERE code LIKE "%0000"';
            if (!$includeGAT) {
                $sql .= ' AND code < "710000"';
            }
            $res = $this->db->query($sql);
            $arr = [];
            while ($row = $res->fetchArray()) {
                $arr[$row['code']] = $row['name'];
            }

            return $arr;
        }

        return array_filter(self::$codes, function ($k) use ($includeGAT) {
            if (!$includeGAT) {
                return substr($k, 2) == '0000' && $k < '710000' ;
            }

            return substr($k, 2) == '0000';
        }, ARRAY_FILTER_USE_KEY);
    }
}
