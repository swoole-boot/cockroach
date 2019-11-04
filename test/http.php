<?php

use cockroach\extensions\EHttp;
/**
 * 引入类自动加载
 */
require __DIR__.'/Autoload.php';

$response = EHttp::put('http://www.baidu.com');
var_dump($response);