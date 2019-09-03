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

$validate['require1'] = EValidate::validate([
    'class' => Required::class
],null);

$require = Container::insure([
    'class' => Required::class
]);

$validate['require2'] = EValidate::validate($require,'');

$validate['require3'] = EValidate::validate($require,'a');

$rule = [
    ['age'],Between::class,'min' => 14,'max' => 20
];

$validate['between1'] = EValidate::rule($rule,[
    'age' => 19
]);

$validate['between2'] = EValidate::rule($rule,[
    'age' => 12
]);

$rules = [
    [
        ['email'],Email::class
    ]
];

$validate['email1'] = EValidate::rules($rules,[
    'email' => 'jhq0113@163.com'
]);

$validate['email2'] = EValidate::rules($rules,[
    'email' => 'jhq0113@163'
]);

$validate['max'] = EValidate::rule([
    ['sendMax'],Max::class,'max' => 25
],[
    'sendMax' => 30
]);

$validate['min'] = EValidate::rule([
    ['sendMax'],Min::class,'min' => 25
],[
    'sendMax' => 30
]);

$validate['url'] = EValidate::rules([
    [
        ['logo'],Url::class
    ],
    [
        ['BigLogo'],Url::class
    ]
],[
    'logo' => 'http://www.baidu.com/1.png',
    'BigLogo' => 'asdfadf'
]);


$validate['phone1'] = EValidate::validate([
    'class' => Phone::class
],'1378765679');

$validate['phone2'] = EValidate::validate([
    'class' => Phone::class
],'13787656829');

$validate['ip1'] = EValidate::rule([
    ['clientIp'],Ip::class
],[
    'clientIp' => '127.0.0.1'
]);

$validate['ip2'] = EValidate::rule([
    ['clientIp'],Ip::class
],[
    'clientIp' => '123123123'
]);

$validate['ip3'] = EValidate::rule([
    ['clientIp'],Ip::class
],[
    'clientIp' => '123:123:12::21'
]);

$validate['length'] = EValidate::validate([
    'class' => Length::class, 'max' => 12, 'min' => 5
],'我也是');

$validate['number'] = EValidate::rule([
    ['longIp'],Number::class
],[
    'longIp' => '123123123'
]);

$validate['callback'] = EValidate::rule([
    ['own'],Callback::class,'function' => function($value) {
        return $value == 'callback';
    }
],[
    'own' => 'callback'
]);

$validate['pattern1'] = EValidate::rule([
    ['phone'],Pattern::class,'pattern' => '/^((1[3|5|8][0-9]))\d{8}$/'
],[
    'phone' => '17156756786'
]);

$validate['pattern2'] = EValidate::rule([
    ['phone'],Pattern::class,'pattern' => '/^((1[3|5|7|8][0-9]))\d{8}$/'
],[
    'phone' => '17567567861'
]);

var_dump($validate);
//exit(json_encode($validate,JSON_UNESCAPED_UNICODE).PHP_EOL);