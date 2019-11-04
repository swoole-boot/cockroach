<?php
namespace cockroach\validators;

/**
 * Class Required
 * @package cockroach\validators
 * @datetime 2019/8/31 11:57 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Required extends Driver
{
    /**
     * @var bool
     * @datetime 2019/9/20 21:29
     * @author roach
     * @email jhq0113@163.com
     */
    public $require = true;

    /**
     * @var bool
     * @datetime 2019/8/31 11:58 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $trim = true;

    /**
     * @param null $value
     * @return bool
     * @datetime 2019/8/31 11:58 AM
     * @author roach
     * @email jhq0113@163.com
     */

    public function validate($field, &$params = [])
    {
        if(!isset($params[ $field ])) {
            return false;
        }

        $params[ $field ] = call_user_func('cockroach\extensions\EFilter::f'.$this->type, $field, $params, $this->default);
        return $this->_validate($params[ $field ]);
    }

    /**
     * @param null $data
     * @return bool
     * Author: Jiang Haiqiang
     * Email : jhq0113@163.com
     * Date: 2018/7/23
     * Time: 18:57
     */
    protected function _validate($data = null)
    {
        if (!isset($data)) {
            return false;
        }

        $data = (string)$data;

        if ($this->trim) {
            return trim($data) !== '';
        }

        return $data !== '';
    }

}