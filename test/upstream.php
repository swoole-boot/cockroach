<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/8/31
 * Time: 3:17 PM
 */
use cockroach\extensions\EUpstream;

require __DIR__.'/Autoload.php';

$servers = [
    [
        'host'      => '10.20.70.31',
        'weight'    => 10
    ],
    [
        'host'      => '10.20.70.51',
        'weight'    => 2
    ],
    [
        'host'      => '10.20.70.62',
        'weight'    => 3
    ],
    [
        'host'      => '10.20.76.81',
        'weight'    => 10
    ],
    [
        'host'      => '10.20.78.91',
        'weight'    => 1
    ],
    [
        'host'      => '10.20.79.42',
        'weight'    => 1
    ],
];
$result['hash'] = EUpstream::consistentHash($servers,'host','11.21.70.12');
$result['shuffle'] = EUpstream::shuffle($servers);
$result['weight']  = EUpstream::weight($servers);

var_dump($result);