<?php
namespace cockroach\base;

/**
 * Class Container
 * @package cockroach\base
 * @datetime 2019/8/30 15:49
 * @author roach
 * @email jhq0113@163.com
 */
class Container extends Extension
{
    /**
     * @var array
     * @datetime 2019/8/30 15:49
     * @author roach
     * @email jhq0113@163.com
     */
    private static $_pool = [];

    /**放入容器
     * @param string $key
     * @param mixed  $value
     * @datetime 2019/8/30 15:51
     * @author roach
     * @email jhq0113@163.com
     */
    static public function set($key,$value)
    {
        self::$_pool[ $key ] = $value;
    }

    /**容器中获取对象
     * @param string $key
     * @return mixed|null
     * @datetime 2019/8/30 16:01
     * @author roach
     * @email jhq0113@163.com
     */
    static public function get($key)
    {
        if(!isset(self::$_pool[ $key ])) {
            return null;
        }

        //如果是配置数组
        if(is_array(self::$_pool[ $key ]) && isset(self::$_pool[ $key ]['class'])) {
            self::$_pool[ $key ] = self::createCockroach(self::$_pool[ $key ]);
        }

        return self::$_pool[ $key ];
    }

    /**装配
     * @param object         $object
     * @param array|object   $config
     * @datetime 2019/8/30 15:53
     * @author roach
     * @email jhq0113@163.com
     */
    static public function assem($object,$config)
    {
        foreach ($config as $property => $value) {
            $object->$property = $value;
        }
    }

    /**创建对象
     * @param array $config
     * @return mixed
     * @datetime 2019/8/30 15:55
     * @author roach
     * @email jhq0113@163.com
     */
    static public function createCockroach(array $config)
    {
        $class = $config['class'];
        unset($config['class']);

        $object = new $class();

        if(method_exists($object,'init')) {
            $object->init($config);
        }else {
            static::assem($object,$config);
        }

        return $object;
    }

    /**如果是对象配置则创建，否则原样返回
     * @param array  $config
     * @param string $defaultClass
     * @return mixed | array
     * @datetime 2019/8/30 15:57
     * @author roach
     * @email jhq0113@163.com
     */
    static public function insure($config,$defaultClass='')
    {
        if(is_array($config)) {
            if(isset($config['class'])) {
                return self::createCockroach($config);
            }

            if($defaultClass !== '') {
                $config['class'] = $defaultClass;
                return self::createCockroach($config);
            }
        }

        return $config;
    }
}