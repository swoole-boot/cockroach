<?php
namespace cockroach\validators;

/**
 * Class Max
 * @package cockroach\validators
 * @datetime 2019/8/31 12:04 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Max extends Between
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
        return $this->_max($data);
    }

}