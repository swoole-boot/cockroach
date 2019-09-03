<?php
namespace cockroach\validators;

/**
 * Class Pattern
 * @package cockroach\validators
 * @datetime 2019/8/31 11:53 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Pattern extends Driver
{
    /**
     * @var string
     * @datetime 2019/8/31 11:53 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $pattern;

    /**
     * @param null $data
     * @return bool
     * @datetime 2019/8/31 11:53 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data = null)
    {
        return preg_match($this->pattern,$data) == 1;
    }
}