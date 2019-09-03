<?php
/**类自动加载器
 * Class Autoload
 * @datetime 2019/8/30 19:26
 * @author roach
 * @email jhq0113@163.com
 */
class Autoload
{
    /**
     * @var array
     * @datetime 2019/8/30 19:27
     * @author roach
     * @email jhq0113@163.com
     */
    private static $_namespaceMap = [];

    /**
     * Autoload constructor.
     */
    private function __construct()
    {
    }

    /**
     * @datetime 2019/8/30 19:27
     * @author roach
     * @email jhq0113@163.com
     */
    private function __clone()
    {
    }

    /**
     * @param $baseNamespace
     * @param $dir
     * @datetime 2019/8/30 19:27
     * @author roach
     * @email jhq0113@163.com
     */
    static public function registerNamespace($baseNamespace,$dir)
    {
        self::$_namespaceMap[ $baseNamespace ] = $dir;
    }

    /**
     * @param $class
     * @datetime 2019/8/30 19:27
     * @author roach
     * @email jhq0113@163.com
     */
    static public function autoload($class)
    {
        $position = strpos($class,'\\');
        $prefix = substr($class,0,$position);

        if(!isset(self::$_namespaceMap[ $prefix ])) {
            return;
        }

        $fileName = self::$_namespaceMap[ $prefix ].str_replace('\\','/',substr($class,$position)).'.php';
        if(file_exists($fileName)) {
            require $fileName;
        }
    }

    /**
     * @datetime 2019/8/30 19:28
     * @author roach
     * @email jhq0113@163.com
     */
    static public function run()
    {
        spl_autoload_register('Autoload::autoload');
    }
}

//自动注册cockroach名称空间
Autoload::registerNamespace('cockroach',dirname(__DIR__).'/src');
Autoload::run();

