<?php
namespace cockroach\orm;

use cockroach\base\Cockroach;
use cockroach\exceptions\RuntimeException;
use cockroach\extensions\EString;
use cockroach\log\Driver;

/**
 * Class Connection
 * @package cockroach\orm
 * @datetime 2020/5/10 10:37 上午
 * @author   roach
 * @email    jhq0113@163.com
 */
abstract class Connection extends Cockroach
{
    /**是否为只读库
     * @var bool
     * @datetime 2020/5/10 1:28 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $readOnly = false;

    /**
     * @var string
     * @datetime 2020/5/10 10:38 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $dsn;

    /**
     * @var string
     * @datetime 2020/5/10 10:39 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $userName;

    /**
     * @var string
     * @datetime 2020/5/10 10:39 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $password;

    /**
     * @var string
     * @datetime 2020/5/10 11:53 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $charset = 'utf8';

    /**
     * @var array
     * @datetime 2020/5/10 10:42 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $attributes;

    /**
     * @var array
     * @datetime 2020/5/10 11:45 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_defaultAttributes = [
        //connect timeout
        \PDO::ATTR_TIMEOUT          => 10,
        \PDO::ATTR_ERRMODE          => \PDO::ERRMODE_EXCEPTION,
    ];

    /**
     * @var bool
     * @datetime 2020/5/10 11:42 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $debug = false;

    /**
     * @var Driver
     * @datetime 2020/5/10 11:36 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $logger;

    /**
     * @var \PDO
     * @datetime 2020/5/10 10:37 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_pdo;

    /**
     * @var int
     * @datetime 2020/5/10 1:01 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_transactionLevel = 0;

    /**
     * @throws RuntimeException
     * @datetime 2020/5/10 12:05 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function open()
    {
        if(!is_null($this->_pdo)) {
            return;
        }

        try {
            $this->attributes = array_merge($this->_defaultAttributes, $this->attributes);
            //连接数据库
            $this->_pdo = new \PDO($this->dsn, $this->userName, $this->password, $this->attributes);
            //设置编码
            $this->_pdo->exec('SET NAMES '.$this->_pdo->quote($this->charset));
        }catch (\PDOException $exception) {
            $msg = EString::interpolate('connect db[{dsn}] failed', [
                'dsn' => $this->dsn,
            ]);
            $this->logger->error($msg);
            $this->_pdo = null;
            throw new RuntimeException($msg);
        }
    }

    /**
     * @param $sql
     * @param array $params
     * @return int
     * @throws RuntimeException
     * @datetime 2020/5/10 1:29 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function execute($sql, $params = [])
    {
        if($this->readOnly) {
            throw new RuntimeException('connection is readonly');
        }

        $this->open();
        $statement = $this->_pdo->prepare($sql);
        $statement->execute($params);

        if($this->debug) {
            $this->logger->info('db sql:[{sql}] params:{params}',[
                'sql'     => $sql,
                'params'  => json_encode($params),
            ]);
        }

        return $statement->rowCount();
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     * @throws RuntimeException
     * @datetime 2020/5/10 1:15 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function queryAll($sql, $params = [])
    {
        $this->open();
        $statement = $this->_pdo->prepare($sql);
        $statement->execute($params);
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();

        if($this->debug) {
            $this->logger->info('db sql:[{sql}] params:{params}',[
                'sql'     => $sql,
                'params'  => json_encode($params),
            ]);
        }

        return $result;
    }

    /**
     * @datetime 2020/5/10 1:06 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function lastInsertId()
    {
        $this->_pdo->lastInsertId();
    }


    /**
     * @return bool
     * @throws RuntimeException
     * @datetime 2020/5/10 1:29 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
     public function begin()
     {
         if($this->readOnly) {
             throw new RuntimeException('connection is readonly');
         }

         if($this->_transactionLevel > 0) {
             return true;
         }

         $this->open();
         $result = $this->_pdo->beginTransaction();
         if($result) {
             $this->_transactionLevel++;
         }
         return $result;
     }

    /**
     * @return bool
     * @datetime 2020/5/10 1:16 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function rollback()
    {
        $this->_transactionLevel = 0;

        return $this->_pdo->rollBack();
    }

    /**
     * @return bool
     * @datetime 2020/5/10 1:23 下午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function commit()
    {
        if($this->_transactionLevel < 1){
            return false;
        }

        $this->_transactionLevel--;
        if($this->_transactionLevel > 0) {
            return true;
        }

        return $this->_pdo->commit();
    }
}