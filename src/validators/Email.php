<?php
namespace cockroach\validators;

/**
 * Class Email
 * @package cockroach\validators
 * @datetime 2019/8/31 11:56 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Email extends Driver
{
    /**
     * @var string
     * @datetime 2019/8/31 11:56 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $msg = 'email格式不正确';

    /**
     * @param null $data
     * @return mixed
     * @datetime 2019/8/31 11:56 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function _validate($data = null)
    {
        return filter_var($data,FILTER_VALIDATE_EMAIL);
    }
}