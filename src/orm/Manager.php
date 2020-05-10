<?php
namespace cockroach\orm;

use cockroach\base\Cockroach;
use cockroach\base\Container;
use cockroach\exceptions\ConfigException;
use cockroach\exceptions\RuntimeException;
use cockroach\log\Driver;

/**
 * Class Manager
 * @package cockroach\orm
 * @datetime 2020/5/10 1:30 下午
 * @author   roach
 * @email    jhq0113@163.com
 */
class Manager extends Cockroach
{
    /**
     * @var array
     * @datetime 2020/5/10 1:30 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $masters = [];

    /**
     * @var array
     * @datetime 2020/5/10 1:30 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $slaves = [];

    /**
     * @var Driver
     * @datetime 2020/5/10 1:41 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $logger;

    /**
     * @var bool
     * @datetime 2020/5/10 1:54 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $debug = false;

    /**
     * @var Connection
     * @datetime 2020/5/10 1:31 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_master;

    /**
     * @var Connection
     * @datetime 2020/5/10 1:32 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_slave;

    /**
     * @param array $config
     * @throws ConfigException
     * @datetime 2020/5/10 1:36 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function init($config = [])
    {
        parent::init($config);

        if(empty($this->masters)) {
            throw new ConfigException('masters can not be empty');
        }

        //如果没有配置从库，自动复用主库
        if(empty($this->slaves)) {
            $this->slaves = $this->masters;
        }
        shuffle($this->masters);
        shuffle($this->slaves);

        //初始化日志
        if(is_null($this->logger)) {
            $this->assemInsure([
                'logger' => [
                    'class' => 'cockroach\log\File',
                ]
            ]);
        }
    }

    /**
     * @return Connection
     * @throws RuntimeException
     * @datetime 2020/5/10 1:48 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function master()
    {
        if(!is_null($this->_master)) {
            return $this->_master;
        }

        foreach ($this->masters as $master) {
            /**
             * @var $connection Connection
             */
            $connection = Container::insure($master);
            $connection->logger = $this->logger;
            $connection->debug  = $this->debug;

            try {
                $connection->open();
            }catch (RuntimeException $exception) {
                continue;
            }

            $this->_master = $connection;
            return $this->_master;
        }

        $this->logger->emergency('All the masters are disabled');
        throw new RuntimeException('All the masters are disabled');
    }

    /**
     * @return Connection
     * @throws RuntimeException
     * @datetime 2020/5/10 1:49 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function slave()
    {
        if(!is_null($this->_slave)) {
            return $this->_slave;
        }

        foreach ($this->slaves as $master) {
            /**
             * @var $connection Connection
             */
            $connection = Container::insure($master);
            $connection->logger   = $this->logger;
            $connection->readOnly = true;
            $connection->debug    = $this->debug;
            try {
                $connection->open();
            }catch (RuntimeException $exception) {
                continue;
            }

            $this->_slave = $connection;
            return $this->_slave;
        }

        $this->logger->emergency('All the slaves are disabled');
        throw new RuntimeException('All the slaves are disabled');
    }
}