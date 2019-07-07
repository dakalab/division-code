<?php

spl_autoload_register(function ($class) {
    include 'src/DivisionCode/DivisionCode.php';
});

use Dakalab\DivisionCode\DivisionCode;

const TOTAL = 1000000;

function runBench($disableSQLite = false)
{
    $t = microtime(true);
    $m = memory_get_usage();

    $divisionCode = new DivisionCode;
    $divisionCode->useSQLite(!$disableSQLite);
    for ($i = 0; $i < TOTAL; $i++) {
        $res = $divisionCode->get('110000');
    }

    $second = round(microtime(true) - $t, 2);
    $memory = round((memory_get_usage() - $m) / 1024, 2);

    echo "Time cost: $second s, Memory cost: $memory kb\n";
}

runBench();

runBench(true);
