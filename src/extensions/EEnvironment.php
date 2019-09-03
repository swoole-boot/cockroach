<?php
namespace cockroach\extensions;

/**
 * Class EEnvironment
 * @package cockroach\extensions
 * @datetime 2019/8/30 18:16
 * @author roach
 * @email jhq0113@163.com
 */
class EEnvironment
{
    /**
     * 生产环境
     */
    const PRODUCT = 'product';

    /**
     * 开发环境
     */
    const DEVELOP = 'develop';

    /**
     * 测试环境
     */
    const TEST    = 'test';

    /**
     * uat环境
     */
    const UAT     = 'uat';

    /**
     * ci环境
     */
    const CI      = 'ci';

    /**获取php.ini自定义配置项值
     * @param string   $key
     * @return string
     * @datetime 2019/8/30 18:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function ini($key)
    {
        return get_cfg_var($key);
    }

    /**获取当前环境
     * @return string
     * @datetime 2019/8/30 18:17
     * @author roach
     * @email jhq0113@163.com
     */
    static public function envir()
    {
        return self::ini('envir');
    }

}