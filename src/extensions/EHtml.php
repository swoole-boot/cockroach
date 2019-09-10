<?php
namespace cockroach\extensions;

/**
 * Class EHtml
 * @package cockroach\extensions
 * @datetime 2019/8/30 18:39
 * @author roach
 * @email jhq0113@163.com
 */
class EHtml
{
    /**
     * @param string $content
     * @param bool   $doubleEncode
     * @return string
     * @datetime 2019/8/30 18:39
     * @author roach
     * @email jhq0113@163.com
     */
    static public function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * @param string $content
     * @return string
     * @datetime 2019/8/30 18:39
     * @author roach
     * @email jhq0113@163.com
     */
    static public function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }
}