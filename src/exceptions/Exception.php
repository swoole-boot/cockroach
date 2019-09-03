<?php
namespace cockroach\exceptions;

/**
 * Class Exception
 * @package cockroach\exceptions
 * @datetime 2019/8/31 11:29 AM
 * @author roach
 * @email jhq0113@163.com
 */
class Exception extends \Exception
{
    /**
     * @return string
     * @datetime 2019/8/31 11:31 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function getName()
    {
        return 'Exception';
    }
}