<?php
namespace cockroach\validators;

/**
 * Class Between
 * @package cockroach\validators
 * @datetime 2019/8/31 12:01 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Between extends Driver
{
    /**
     * @var bool
     * @datetime 2019/8/31 12:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $allowEqual = true;

    /**精度
     * @var int
     * @datetime 2019/8/31 12:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $precision = 2;

    /**最小值
     * @var
     * @datetime 2019/8/31 12:01 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $min;

    /**最大值
     * @var
     * @datetime 2019/8/31 12:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $max;

    /**
     * @param $data
     * @return bool
     * @datetime 2019/8/31 12:02 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function _max($data)
    {
        return $this->allowEqual ? (bccomp($data,$this->max,$this->precision) <= 0) : (bccomp($data,$this->max,$this->precision) < 0);
    }

    /**
     * @param $data
     * @return bool
     * @datetime 2019/8/31 12:03 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function _min($data)
    {
        return $this->allowEqual ? (bccomp($data,$this->min,$this->precision) >= 0) : (bccomp($data,$this->min,$this->precision) > 0);
    }

    /**
     * @param null $data
     * @return bool
     * @datetime 2019/8/31 12:03 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data=null)
    {
        return $this->_max($data) && $this->_min($data);
    }
}