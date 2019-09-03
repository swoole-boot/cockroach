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

    /**验证
     * @param mixed $value
     * @return bool
     * @datetime 2019/8/31 11:12 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function validate($value = null)
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