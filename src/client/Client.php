<?php
namespace cockroach\client;

use cockroach\base\Cockroach;

/**
 * Class Client
 * @package cockroach\client
 * @datetime 2019/9/13 16:28
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Client extends Cockroach
{
    /**
     * @var string
     * @datetime 2019/9/13 16:29
     * @author roach
     * @email jhq0113@163.com
     */
    public $host;

    /**
     * @var int
     * @datetime 2019/9/13 16:29
     * @author roach
     * @email jhq0113@163.com
     */
    public $port;

    /**
     * @var int
     * @datetime 2019/9/13 16:50
     * @author roach
     * @email jhq0113@163.com
     */
    public $timeout = 3;

    /**
     * @var
     * @datetime 2019/9/13 16:33
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_client;

    /**
     * @return mixed
     * @datetime 2019/9/13 16:54
     * @author roach
     * @email jhq0113@163.com
     */
    public function client()
    {
        if(is_null($this->_client)) {
            $this->_client = $this->createClient();
        }

        return $this->_client;
    }

    /**
     * @return mixed
     * @datetime 2019/9/13 16:53
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function createClient();

    /**
     * @param array $params
     * @return mixed
     * @datetime 2019/9/13 16:30
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function request($params = []);

    /**
     * @param string $func
     * @param array  $params
     * @return mixed
     * @datetime 2019/9/13 17:08
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function call($func,$params);
}