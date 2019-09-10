<?php
namespace cockroach\validators;

/**
 * Class Min
 * @package cockroach\validators
 * @datetime 2019/8/31 12:05 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Min extends Between
{
    /**
     * @param null $data
     * @return bool
     * @datetime 2019/8/31 12:05 PM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _validate($data = null)
    {
        return $this->_min($data);
    }
}