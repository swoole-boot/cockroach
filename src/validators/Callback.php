<?php
namespace cockroach\validators;

/**
 * Class Callback
 * @package cockroach\validators
 * @datetime 2019/8/31 12:08 PM
 * @author roach
 * @email jhq0113@163.com
 */
class Callback extends Driver
{
    /**
     * @var callable
     * @datetime 2019/8/31 12:07 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public $function;

    /**
     * @param null $data
     * @return mixed
     * @datetime 2019/8/31 12:07 PM
     * @author roach
     * @email jhq0113@163.com
     */
    public function _validate($data = null)
    {
        return call_user_func($this->function,$data);
    }

}