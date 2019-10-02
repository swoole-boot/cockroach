<?php
/**
 * Created by PhpStorm.
 * User: Jiang Haiqiang
 * Date: 2019/8/31
 * Time: 12:21 PM
 */
use cockroach\validators\Email;
use cockroach\extensions\EValidate;
use cockroach\validators\Between;
use cockroach\validators\Min;
use cockroach\validators\Max;
use cockroach\validators\Phone;
use cockroach\validators\Url;
use cockroach\validators\Ip;
use cockroach\validators\Required;
use cockroach\validators\Length;
use cockroach\validators\Number;
use cockroach\validators\Pattern;
use cockroach\validators\Callback;
use cockroach\base\Container;

require __DIR__.'/Autoload.php';



$require = Container::insure([
    'class' => Required::class
]);


$rule = [
    ['age'],Between::class,'min' => 14,'max' => 20
];

$params = [
    'age' => 19
];
$validate['between1'] = EValidate::rule($rule,$params);
