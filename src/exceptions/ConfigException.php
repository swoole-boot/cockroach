<?php
namespace cockroach\exceptions;

/**
 * Class ConfigException
 * @package cockroach\exceptions
 * @datetime 2019/8/31 11:33 AM
 * @author roach
 * @email jhq0113@163.com
 */
class ConfigException extends Exception
{
    /**
     * @return string
     * @datetime 2019/8/31 11:33 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function getName()
    {
        return 'ConfigException';
    }
}