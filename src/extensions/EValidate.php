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
    /**单规则验证，没有错误返回空数组
     * @param array  $rule
     * @param array  $params
     * @param bool   $all      遇到验证未通过是否还验证所有，默认不验证所有
     * @return array
     * @datetime 2019/8/31 12:18 PM
     * @author roach
     * @email jhq0113@163.com
     */
    static public function rule($rule, array &$params= [], $all = false)
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
            $result = $validate->validate($field,$params);
            if(!$result) {
                if(!isset($errors[ $field ])) {
                    $errors[ $field ] = [];
                }
                array_push($errors[ $field ],$validate->msg);
                if(!$all) {
                    return $errors;
                }
            }
        }

        return $errors;
    }


    /**多规则验证，没有错误返回空数组
     * @param array $rules
     * @param array $params
     * @param bool  $all      遇到验证未通过是否还验证所有，默认不验证所有
     * @return array
     * @datetime 2019/9/20 20:59
     * @author roach
     * @email jhq0113@163.com
     */
    static public function rules($rules, array &$params = [] , $all = false)
    {
        $errors = [];

        foreach ($rules as $rule) {
            $result = static::rule($rule,$params);
            if(!empty($result)) {
                if ($all) {
                    $errors = EArray::merge($errors,$result);
                } else {
                    return $result;
                }
            }
        }

        return $errors;
    }
}