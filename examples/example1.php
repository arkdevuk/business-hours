<?php

namespace App;

use ArkdevukBusinessHours\classes\BHRange;
use ArkdevukBusinessHours\classes\BHWrapper;

require_once __DIR__.'/../vendor/autoload.php';

$timezone = 'Europe/Paris';
$timezone2 = 'Europe/Kiev';

$wrapper = new BHWrapper($timezone);

$r1 = new BHRange([
    'mon' => true,
    'tue' => true,
    'wed' => true,
    'thu' => true,
    'fri' => true,
    'sat' => false,
    'sun' => false,
    //time
    'start' => '09:00',
    'end' => '12:00',
]);

$r2 = new BHRange([
    'mon' => true,
    'tue' => true,
    'wed' => true,
    'thu' => true,
    'fri' => true,
    'sat' => false,
    'sun' => false,
    //time
    'start' => '14:00',
    'end' => '18:00',
]);

$wrapper->addRange($r1);
$wrapper->addRange($r2);

$locale = 'fr_FR';
$options = [
    'mode' => 'details',
];

echo $wrapper->toString($options, $locale, $timezone);


echo PHP_EOL.'======= TIME ZONE +1 ======='.PHP_EOL;

echo $wrapper->toString($options, $locale, $timezone2);


echo PHP_EOL.PHP_EOL;
echo 'Hello 👋';