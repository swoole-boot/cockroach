<?php
namespace cockroach\exceptions;

/**
 * Class ErrorException
 * @package cockroach\exceptions
 * @datetime 2019/8/31 11:32 AM
 * @author roach
 * @email jhq0113@163.com
 */
class ErrorException extends \ErrorException
{
    /**
     * @var array
     * @datetime 2019/8/31 11:31 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public static $ERROR_MAP = [
        E_COMPILE_ERROR         => 'PHP Compile Error',
        E_COMPILE_WARNING       => 'PHP Compile Warning',
        E_CORE_ERROR            => 'PHP Core Error',
        E_CORE_WARNING          => 'PHP Core Warning',
        E_DEPRECATED            => 'PHP Deprecated Warning',
        E_ERROR                 => 'PHP Fatal Error',
        E_NOTICE                => 'PHP Notice',
        E_PARSE                 => 'PHP Parse Error',
        E_RECOVERABLE_ERROR     => 'PHP Recoverable Error',
        E_STRICT                => 'PHP Strict Warning',
        E_USER_DEPRECATED       => 'PHP User Deprecated Warning',
        E_USER_ERROR            => 'PHP User Error',
        E_USER_NOTICE           => 'PHP User Notice',
        E_USER_WARNING          => 'PHP User Warning',
        E_WARNING               => 'PHP Warning',
    ];

    /**
     * @param array $error
     * @return bool
     * @datetime 2019/8/31 11:32 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public static function isFatalError($error)
    {
        return isset($error['type']) && in_array($error['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING ]);
    }

    /**
     * @return string
     * @datetime 2019/8/31 11:32 AM
     * @author roach
     * @email jhq0113@163.com
     */
    public function getName()
    {
        $code = $this->getCode();
        return isset(static::$ERROR_MAP[ $code ]) ? static::$ERROR_MAP[ $code ] : 'Error';
    }
}