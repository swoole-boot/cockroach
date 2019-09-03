<?php
namespace cockroach\validators;

/**
 * Class Length
 * @package cockroach\validator
 * @datetime 2019/8/31 11:16 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Length extends Driver
{
    /**
     * @var string
     * @datetime 2019/8/31 11:14 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $charset = 'utf-8';

    /**最大长度
     * @var int
     * @datetime 2019/8/31 11:15 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $max;

    /**最小长度
     * @var int
     * @datetime 2019/8/31 11:15 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $min = 0;

    /**验证
     * @param mixed $data
     * @return bool
     * @datetime 2019/8/31 11:16 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data = null)
    {
        if (!is_null($data)) {
            $length = mb_strlen($data,$this->charset);

            if ($length < $this->min) {
                return false;
            }

            if (isset($this->max) && ($length > $this->max)) {
                return false;
            }

            return true;
        }

        return false;
    }

}