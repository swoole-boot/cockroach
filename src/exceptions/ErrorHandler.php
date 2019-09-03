<?php
namespace cockroach\exceptions;

use cockroach\base\Cockroach;
use cockroach\extensions\ECli;

/**
 * Class ErrorHandler
 * @package cockroach\exceptions
 * @datetime 2019/8/31 11:35 AM
 * @author roach
 * @email jhq0113@163.com
 */
class ErrorHandler extends Cockroach
{
    /**
     * @var int
     * @datetime 2019/8/31 11:35 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $memoryReserveSize = 262144;

    /**
     * @var \Throwable
     * @datetime 2019/8/31 11:35 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public $exception;

    /**
     * @var string
     * @datetime 2019/8/31 11:36 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected $_memoryReserve;

    /**
     * @datetime 2019/8/31 11:36 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * @datetime 2019/8/31 11:36 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function register()
    {
        ini_set('display_errors', false);
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);

        if ($this->memoryReserveSize > 0) {
            $this->_memoryReserve = str_repeat('x', $this->memoryReserveSize);
        }
        register_shutdown_function([$this, 'handleFatalError']);
    }

    /**
     * @param \Throwable $exception
     * @datetime 2019/8/31 11:37 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function handleException($exception)
    {
        $this->exception = $exception;

        //在处理异常时禁用错误捕获以避免递归错误
        $this->unregister();

        try {
            $this->renderException($exception);
        } catch (\Exception $e) {
            //兼容php5
            $this->_handleFallbackExceptionMessage($e, $exception);
        } catch (\Throwable $e) {
            //php7
            $this->_handleFallbackExceptionMessage($e, $exception);
        }

        $this->exception = null;
    }

    /**处理error
     * @param int     $code
     * @param string  $message
     * @param string  $file
     * @param int     $line
     * @return bool
     * @throws ErrorException
     * @datetime 2019/8/31 11:37 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function handleError($code, $message, $file, $line)
    {
        //判断错误等级
        if (error_reporting() & $code) {
            //防止类自动加载不能用
            if (!class_exists('cockroach\\exceptions\\ErrorException', false)) {
                require_once __DIR__ . '/ErrorException.php';
            }
            $exception = new ErrorException($message, $code, $code, $file, $line);
            // 如果在__toString方法中出现的错误，我们不应该抛出任何异常
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->handleException($exception);
                    exit(1);
                }
            }
            throw $exception;
        }

        return false;
    }

    /**
     * @datetime 2019/8/31 11:38 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function handleFatalError()
    {
        unset($this->_memoryReserve);
        //防止类自动加载不能用
        if (!class_exists('base\\exception\\ErrorException', false)) {
            require_once __DIR__ . '/ErrorException.php';
        }

        $error = error_get_last();
        if (ErrorException::isFatalError($error)) {
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->exception = $exception;
            $this->renderException($exception);
            exit(1);
        }
    }

    /**显示异常的时候引发了异常
     * @param \Throwable $exception
     * @param \Throwable $previousException
     * @datetime 2019/8/31 11:39 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _handleFallbackExceptionMessage($exception, $previousException)
    {
        $msg = "An Error occurred while handling another error:\n";
        $msg .= (string) $exception;
        $msg .= "\nPrevious exception:\n";
        $msg .= (string) $previousException;
        error_log($msg);
        exit(1);
    }

    /**渲染异常
     * @param \Throwable $exception
     * @datetime 2019/8/31 11:39 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function renderException($exception)
    {
        if(ECli::isCli()) {
            return $this->_showException4Cli($exception);
        }

        return $this->_showException4Web($exception);
    }

    /**
     * @param \Throwable $exception
     * @param bool       $isRecursion
     * @datetime 2019/8/31 11:41 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _showException4Cli($exception,$isRecursion = false)
    {
        if(!$isRecursion) {
            ECli::error('Cockroach Error:');
        }

        $export = function($e) {
            /**
             * @var \Throwable $e
             */
            ECli::warn('异常码:{code},异常文件:{file},异常行数:{line}',[
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            ECli::info('异常详情：'.$e->getMessage());

            //换行
            echo PHP_EOL;

            $previous = $e->getPrevious();
            if($previous) {
                $this->_showException4Cli($previous,true);
            }
        };

        $export($exception);

        unset($_SERVER['LS_COLORS']);
        ECli::info('$_SEVER = {server} ',[
            'server' => json_encode($_SERVER,JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * @param \Throwable $exception
     * @param bool       $isRecursion
     * @datetime 2019/8/31 11:40 AM
     * @author roach
     * @email jhq0113@163.com
     */
    protected function _showException4Web($exception,$isRecursion = false)
    {
        if(!$isRecursion) {
            $html = '<div style="margin: 10px 0;text-align: center;">';
            $html.= '<h1>Cockroach Error</h1>';
            $html.='<div style="margin: 10px 0;text-align: left;">';
        }

        $export = function($e) use(&$html)  {
            /**
             * @var \Throwable $e
             */
            $html.='<hr/>';
            $html.='<div><h4>异常码</h4><p>'.$e->getCode().'</p></div>';
            $html.='<div><h4>异常文件</h4><p>'.$e->getFile().'</p></div>';
            $html.='<div><h4>异常出现行数</h4><p>'.$e->getLine().'</p></div>';
            $html.='<div><h4>异常信息详情</h4><p>'.$e->getMessage().'</p></div>';
            $previous = $e->getPrevious();
            if($previous) {
                $this->_showException4Web($previous,true);
            } else {
                $html.= '</div>';
            }
        };

        $export($exception);

        $html.='</div>';

        echo $html;
    }

}