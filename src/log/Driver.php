<?php
namespace cockroach\log;

use cockroach\base\Cockroach;

/**
 * Class Driver
 * @package cockroach\log
 * @datetime 2019/9/10 13:19
 * @author roach
 * @email jhq0113@163.com
 */
abstract class Driver extends Cockroach implements ILog
{
    const LEVEL_EMERGENCY = 'EMERGENCY';    //系统不可用
    const LEVEL_ALERT     = 'ALERT';        //**必须**立刻采取行动
    const LEVEL_CRITICAL  = 'CRITICAL';     //紧急情况
    const LEVEL_ERROR     = 'ERROR';        //运行时出现的错误，不需要立刻采取行动，但必须记录下来以备检测。
    const LEVEL_WARNING   = 'WARNING';      //出现非错误性的异常。
    const LEVEL_NOTICE    = 'NOTICE';       //一般性重要的事件。
    const LEVEL_INFO      = 'INFO';         //重要事件
    const LEVEL_DEBUG     = 'DEBUG';        //debug 详情

    /**
     * 级别
     */
    const LEVELS =[
        'EMERGENCY'     => 0,
        'ALERT'         => 1,
        'CRITICAL'      => 2,
        'ERROR'         => 3,
        'WARNING'       => 4,
        'NOTICE'        => 5,
        'INFO'          => 6,
        'DEBUG'         => 7,
        'ALL'           => 8,
    ];

    /**设置级别
     * @var int
     * @datetime 2019/9/10 13:22
     * @author roach
     * @email jhq0113@163.com
     */
    public $level = 8;

    /**系统不可用
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function emergency($message, array $context = [])
    {
        return $this->log(self::LEVEL_EMERGENCY,$message,$context);
    }

    /**
     * **必须**立刻采取行动
     * 例如：在整个网站都垮掉了、数据库不可用了或者其他的情况下，**应该**发送一条警报短信把你叫醒。
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function alert($message, array $context = [])
    {
        return $this->log(self::LEVEL_ALERT,$message,$context);
    }

    /**
     * 紧急情况
     * 例如：程序组件不可用或者出现非预期的异常。
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function critical($message, array $context = [])
    {
        return $this->log(self::LEVEL_CRITICAL,$message,$context);
    }

    /**
     * 运行时出现的错误，不需要立刻采取行动，但必须记录下来以备检测。
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function error($message, array $context = [])
    {
        return $this->log(self::LEVEL_ERROR,$message,$context);
    }

    /**
     * 出现非错误性的异常。
     * 例如：使用了被弃用的API、错误地使用了API或者非预想的不必要错误。
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function warning($message, array $context = [])
    {
        return $this->log(self::LEVEL_WARNING,$message,$context);
    }

    /**
     * 一般性重要的事件。
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function notice($message, array $context = [])
    {
        return $this->log(self::LEVEL_NOTICE,$message,$context);
    }

    /**
     * 重要事件
     * 例如：用户登录和SQL记录。
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function info($message, array $context = [])
    {
        return $this->log(self::LEVEL_INFO,$message,$context);
    }

    /**
     * debug 详情
     * @param string $message
     * @param array  $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    public function debug($message, array $context = [])
    {
        return $this->log(self::LEVEL_DEBUG,$message,$context);
    }

    /**打任意级别的日志
     * @param string  $level
     * @param string  $message
     * @param array   $context
     * @return null
     * @datetime 2019/9/10 13:26
     * @author roach
     * @email jhq0113@163.com
     */
    abstract public function log($level, $message,array $context=[]);
}