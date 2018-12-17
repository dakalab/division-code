# division code

Administrative division codes of China (http://www.mca.gov.cn/article/sj/xzqh/)

[![Build Status](https://travis-ci.org/dakalab/division-code.svg?branch=master)](https://travis-ci.org/dakalab/division-code)
[![codecov](https://codecov.io/gh/dakalab/division-code/branch/master/graph/badge.svg)](https://codecov.io/gh/dakalab/division-code)
[![Latest Stable Version](https://poser.pugx.org/dakalab/division-code/v/stable)](https://packagist.org/packages/dakalab/division-code)
[![Total Downloads](https://poser.pugx.org/dakalab/division-code/downloads)](https://packagist.org/packages/dakalab/division-code)
[![PHP Version](https://img.shields.io/php-eye/dakalab/division-code.svg)](https://packagist.org/packages/dakalab/division-code)
[![License](https://poser.pugx.org/dakalab/division-code/license.svg)](https://packagist.org/packages/dakalab/division-code)

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
