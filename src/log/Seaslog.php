<?php
namespace cockroach\log;

use cockroach\extensions\EString;

/**
 * Class Seaslog
 * @package cockroach\log
 * @datetime 2019/9/10 13:15
 * @author roach
 * @email jhq0113@163.com
 */
class Seaslog extends Driver
{
    /**
     * @var string
     * @datetime 2019/9/10 13:35
     * @author roach
     * @email jhq0113@163.com
     */
    public $app = 'cockroach';

    /**
     * @var string
     * @datetime 2019/9/10 13:34
     * @author roach
     * @email jhq0113@163.com
     */
    public $basePath = '/tmp/logs/seaslog';

    /**
     * @param array $config
     * @datetime 2019/9/10 13:30
     * @author roach
     * @email jhq0113@163.com
     */
    public function init($config = [])
    {
        parent::init($config);

        $this->_initSeaslog();
    }

    /**
     * @param string $requestId
     * @datetime 2019/9/13 14:49
     * @author roach
     * @email jhq0113@163.com
     */
    public function setRequestId($requestId)
    {
        \SeasLog::setRequestID($requestId);
    }

    /**
     * @datetime 2019/9/10 13:31
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _initSeaslog()
    {
        ini_set('seaslog.level',$this->level);

        \SeasLog::setBasePath($this->basePath);
        \SeasLog::setRequestID(EString::requestId($this->app));
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void|null
     * @datetime 2019/9/10 13:29
     * @author roach
     * @email jhq0113@163.com
     */
    public function log($level, $message, array $context = [])
    {
        \SeasLog::log($level,$message,$context,$this->app);
    }
}