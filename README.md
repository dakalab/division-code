# division code

[![Build Status](https://travis-ci.org/dakalab/division-code.svg?branch=master)](https://travis-ci.org/dakalab/division-code)
[![Codecov](https://codecov.io/gh/dakalab/division-code/branch/master/graph/badge.svg)](https://codecov.io/gh/dakalab/division-code)
[![Latest Stable Version](https://poser.pugx.org/dakalab/division-code/v/stable)](https://packagist.org/packages/dakalab/division-code)
[![Total Downloads](https://poser.pugx.org/dakalab/division-code/downloads)](https://packagist.org/packages/dakalab/division-code)
[![License](https://poser.pugx.org/dakalab/division-code/license.svg)](https://packagist.org/packages/dakalab/division-code)

Administrative division codes of China (http://www.mca.gov.cn/article/sj/xzqh/)

This library has two ways of storage: php array file and SQLite3 database, see the benchmark below. The library will automatically detect if your php support SQLite3, if yes then it will use SQLite3, otherwise it will fall back to use php array.

You can also use function `useSQLite($v = true)` to turn on or turn off using SQLite.

## Install

```
composer require dakalab/division-code
```

## Usage

```
use Dakalab\DivisionCode\DivisionCode;

$divisionCode = new DivisionCode;
$res = $divisionCode->get('110000'); // 北京市
```

## Upgrade

If you want to upgrade the division codes by yourself, you can simply run the `Upgrader`

```
use Dakalab\DivisionCode\Upgrader;

$upgrader = new Upgrader;
$upgrader->upgrade();
```

## Benchmark

For a loop of 1000000 calls ran on a MacBook Pro 2.9 GHz Intel Core i5, 8GB 1867 MHz DDR3:

**Use sqlite3**

Time cost: 23.28 s, Memory cost: 15.85 kb

**Use php array**

Time cost: 0.47 s, Memory cost: 287.45 kb

**Conclusion**

SQLite3 uses less memory usage but slower, built-in php array is much faster but costs much more memory.
