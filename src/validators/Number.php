<?php
namespace cockroach\validators;

/**
 * Class Number
 * @package cockroach\validators
 * @datetime 2019/8/31 12:00 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Number extends Driver
{
    /**
     * @var string
     * @datetime 2019/8/31 12:00 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $msg = '该项不是一个有效的数字';

    /**
     * @param null $data
     * @return bool
     * @datetime 2019/8/31 12:00 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data = null)
    {
        return is_numeric($data);
    }
}