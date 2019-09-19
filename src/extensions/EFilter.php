<?php
namespace cockroach\extensions;

use cockroach\base\Extension;

/**
 * Class EFilter
 * @package cockroach\extensions
 * @datetime 2019/9/19 18:50
 * @author roach
 * @email jhq0113@163.com
 */
class EFilter extends Extension
{
    /**过滤字符串参数
     * @param string $key            键
     * @param array  $data          数据数组
     * @param string $defaultValue  默认值
     * @return string
     * @datetime 2019/9/19 18:52
     * @author roach
     * @email jhq0113@163.com
     */
    static public function fStr($key, $data, $defaultValue = '')
    {
        if(!isset($data[ $key ])) {
            return $defaultValue;
        }
        return addslashes(trim($data[ $key ]));
    }

    /**过滤int参数
     * @param string $key            键
     * @param array  $data          数据数组
     * @param string $defaultValue  默认值
     * @return int
     * @datetime 2019/9/19 18:52
     * @author roach
     * @email jhq0113@163.com
     */
    static public function fInt($key, $data, $defaultValue = 0)
    {
        if(!isset($data[ $key ])) {
            return $defaultValue;
        }

        return (int)trim($data[ $key ]);
    }

    /**过滤float参数
     * @param string $key            键
     * @param array  $data          数据数组
     * @param string $defaultValue  默认值
     * @return float|int
     * @datetime 2019/9/19 18:51
     * @author roach
     * @email jhq0113@163.com
     */
    static public function fFloat($key, $data, $defaultValue = 0)
    {
        if(!isset($data[ $key ])) {
            return $defaultValue;
        }

        return (float)trim($data[ $key ]);
    }
}