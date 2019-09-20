<?php
namespace cockroach\validators;

use cockroach\base\Cockroach;

/**
 * Class Driver
 * @package cockroach\validator
 * @datetime 2019/8/31 11:11 AM
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Driver extends Cockroach
{
    const TYPE_STRING = '1';
    const TYPE_INT    = '2';
    const TYPE_FLOAT  = '3';

    /**
     * @var string
     * @datetime 2019/8/31 11:11 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $msg = '格式不正确';

    /**是否允许为null
     * @var bool
     * @datetime 2019/8/31 11:12 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $allowNull = false;

    /**默认值
     * @var
     * @datetime 2019/9/20 13:20
     * @author roach
     * @email jhq0113@163.com
     */
    public $default = null;

    /**数据类型
     * @var string
     * @datetime 2019/9/20 13:21
     * @author roach
     * @email jhq0113@163.com
     */
    public $type = self::TYPE_STRING;

    /**验证
     * @param mixed $value
     * @return bool
     * @datetime 2019/8/31 11:12 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function validate($field, &$params = [])
    {
        if(is_null($value)) {
            return $this->allowNull;
        }

        return $this->_validate($value);
    }

    /**验证
     * @param mixed $data
     * @return bool
     * @datetime 2019/8/31 11:13 AM
     * @author roach
     * @email jhq0113@163.com
     */
    abstract protected function _validate($data = null );
}