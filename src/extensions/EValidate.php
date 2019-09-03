<?php
namespace cockroach\extensions;

use cockroach\base\Container;
use cockroach\base\Extension;
use cockroach\validators\Driver;

/**
 * Class EValidate
 * @package cockroach\extensions
 * @datetime 2019/8/31 12:10 PM
 * @author roach
 * @email jhq0113@163.com
 */
class EValidate extends Extension
{
    /**验证
     * @param array|Driver $validate
     * @param mixed        $value
     * @return bool
     * @datetime 2019/8/31 12:13 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function validate($validate,$value)
    {
        return Container::insure($validate)->validate($value);
    }

    /**单规则验证，没有错误返回空数组
     * @param array  $rule
     * @param array  $params
     * @return array
     * @datetime 2019/8/31 12:18 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function rule($rule,array $params= [])
    {
        $fields = $rule[0];
        $rule['class'] = $rule[1] ;

        unset($rule[0],$rule[1]);

        /**
         * @var Driver $validate
         */
        $validate = Container::insure($rule);

        //错误信息
        $errors = [];
        foreach ($fields as $field) {
            $result = $validate->validate($params[ $field ]);

            if(!$result) {
                if(!isset($errors[ $field ])) {
                    $errors[ $field ] = [];
                }

                array_push($errors[ $field ],$validate->msg);
            }
        }

        return $errors;
    }

    /**多规则验证，没有错误返回空数组
     * @param array $rules
     * @param array $params
     * @return array
     * @datetime 2019/8/31 12:20 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function rules($rules,array $params = [])
    {
        $errors = [];

        foreach ($rules as $rule) {
            $result = static::rule($rule,$params);
            if(!empty($result)) {
                $errors = EArray::merge($errors,$result);
            }
        }

        return $errors;
    }
}