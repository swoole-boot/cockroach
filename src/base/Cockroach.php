<?php
namespace cockroach\base;

/**所有类基类
 * Class Cockroach
 * @package cockroach\base
 * @datetime 2019/8/30 15:41
 * @author roach
 * @email jhq0113@163.com
 */
class Cockroach
{
    /**
     * @param array $config
     * @datetime 2019/8/31 11:19 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function assemInsure($config)
    {
        foreach ($config as $property => $value) {
            $this->$property = Container::insure($value);
        }
    }

    /**
     * @param array $config
     * @datetime 2019/8/31 11:19 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        $this->assemInsure($config);
    }
}