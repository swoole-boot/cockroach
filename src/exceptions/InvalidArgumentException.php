<?php
namespace cockroach\exceptions;

/**
 * Class InvalidArgumentException
 * @package cockroach\exceptions
 * @datetime 2019/9/16 11:25
 * @author roach
 * @email jhq0113@163.com
 */
class InvalidArgumentException extends Exception
{
    /**
     * @return string
     * @datetime 2019/8/31 11:33 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function getName()
    {
        return 'InvalidArgumentException';
    }
}