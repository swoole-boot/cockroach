<?php
namespace cockroach\validators;

/**
 * Class Phone
 * @package cockroach\validators
 * @datetime 2019/8/31 12:08 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Phone extends Pattern
{
    /**
     * @var string
     * @datetime 2019/8/31 12:09 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $pattern = '/^((1[3|5|6|7|8|9][0-9]))\d{8}$/';
}