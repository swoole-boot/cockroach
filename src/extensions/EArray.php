<?php
namespace cockroach\extensions;

/**
 * Class EArray
 * @package cockroach\extensions
 * @datetime 2019/8/30 18:07
 * @author roach
 * @email jhq0113@163.com
 */
class EArray
{
    /**数组递归合并
     * @param array array1
     * @param array array2
     * @param array arrayN
     * @return array
     * @datetime 2019/8/30 18:07
     * @author roach
     * @email jhq0113@163.com
     */
    static public function merge($array1,$array2)
    {
        $args = func_get_args();
        $result = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $result)) {
                        $result[] = $v;
                    } else {
                        $result[$k] = $v;
                    }
                } elseif (is_array($v) && isset($result[$k]) && is_array($result[$k])) {
                    $result[$k] = self::merge($result[$k], $v);
                } else {
                    $result[$k] = $v;
                }
            }
        }
        return $result;
    }
}