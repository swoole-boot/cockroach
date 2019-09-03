<?php
namespace cockroach\validators;

/**
 * Class Ip
 * @package cockroach\validators
 * @datetime 2019/8/31 11:54 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Ip extends Driver
{
    /**
     * @var string
     * @datetime 2019/8/31 11:55 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $msg = 'ip地址不正确';

    /**
     * @param null $data
     * @return bool|mixed
     * @datetime 2019/8/31 11:54 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data = null)
    {
        return filter_var($data,FILTER_VALIDATE_IP);
    }
}