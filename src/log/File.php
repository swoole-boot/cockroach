<?php
namespace cockroach\log;

use cockroach\extensions\EHttp;
use cockroach\extensions\EString;

/**
 * Class File
 * @package cockroach\log
 * @datetime 2020/5/10 11:08 上午
 * @author   roach
 * @email    jhq0113@163.com
 */
class File extends Driver
{
    /**
     * @var string
     * @datetime 2020/5/10 11:11 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $app = 'cockroach';

    /**
     * @var string
     * @datetime 2020/5/10 11:08 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public $basePath = '/tmp/logs';

    /**
     * @var string
     * @datetime 2020/5/10 11:16 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_fileName;

    /**当前进程id
     * @var string
     * @datetime 2020/5/10 11:32 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_processId;

    /**
     * @var string
     * @datetime 2020/5/10 11:34 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    protected $_clientIp;

    /**
     * @param array $config
     * @datetime 2020/5/10 11:10 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function init($config = [])
    {
        parent::init($config);

        $this->_fileName  = rtrim($this->basePath.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR).date('Y').DIRECTORY_SEPARATOR.date('m-d-').$this->app.'-log';
        $this->_requestId = EString::requestId($this->app);
        $this->_processId = getmypid();
        $this->_clientIp  = EHttp::getClientIp();
    }

    /**记录日志
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void|null
     * @datetime 2020/5/10 11:24 上午
     * @author   roach
     * @email    jhq0113@163.com
     */
    public function log($level, $message, array $context = [])
    {
        //判断日志级别
        if(self::LEVELS[ $level ] > $this->level) {
            return;
        }

        if(!empty($context)) {
            $message = EString::interpolate($message, $context);
        }

        $data = json_encode([
            'dateTime'    => date('Y-m-d H:i:s'),
            'level'       => $level,
            'clientIp'    => $this->_clientIp,
            'method'      => $_SERVER['REQUEST_METHOD'],
            'host'        => $_SERVER['HTTP_HOST'],
            'uri'         => $_SERVER['REQUEST_URI'],
            'msg'         => $message,
            'requestId'   => $this->_requestId,
            'processId'   => $this->_processId,
        ], JSON_UNESCAPED_UNICODE);

        @file_put_contents($this->_fileName, $data, FILE_APPEND | LOCK_EX);
    }
}