<?php
namespace cockroach\validators;

use cockroach\base\Cockroach;
use cockroach\extensions\EFilter;

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

    /**是否必传
     * @var bool
     * @datetime 2019/8/31 11:12 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $require = false;

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
    public $type = EFilter::TYPE_STRING;

    /**验证
     * @param string $field
     * @param array  $params
     * @return bool
     * @datetime 2019/9/20 20:36
     * @author roach
     * @email jhq0113@163.com
     */
    public function validate($field, &$params = [])
    {
        if(!isset($params[ $field ])) {
            //必填字段
            if($this->require) {
               return false;
            }else if(isset($this->type)) {  //指定类型
                $params[ $field ] = call_user_func('cockroach\extensions\EFilter::f'.$this->type, $field, $params, $this->default);
                return true;
            }
            //指定默认值
            $params[ $field ] = $this->default;
            return true;
        }

        $params[ $field ] = call_user_func('cockroach\extensions\EFilter::f'.$this->type, $field, $params, $this->default);
        return $this->_validate($params[ $field ]);
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