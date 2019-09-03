<?php
namespace cockroach\validators;

/**
 * Class Url
 * @package cockroach\validators
 * @datetime 2019/8/31 11:51 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Url extends Driver
{
    /**
     * @var string
     * @datetime 2019/8/31 11:51 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $msg = 'url格式不正确';

    /**验证
     * @param null $data
     * @return mixed
     * @datetime 2019/8/31 11:51 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data = null)
    {
        return filter_var($data,FILTER_VALIDATE_URL);
    }
}