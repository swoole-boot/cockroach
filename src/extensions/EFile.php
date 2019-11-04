<?php
namespace cockroach\extensions;

/**
 * Class EFile
 * @package cockroach\extensions
 * @datetime 2019/8/30 18:29
 * @author roach
 * @email jhq0113@163.com
 */
class EFile
{
    /**递归创建目录
     * @param string $dir
     * @param int    $mode
     * @datetime 2019/8/30 18:29
     * @author roach
     * @email jhq0113@163.com
     */
    static public function mkdir($dir,$mode=0755)
    {
        if(!is_dir($dir)) {
            mkdir($dir,$mode,true);
        }
    }

    /**同步写文件
     * @param string $fileName
     * @param string $content
     * @param bool   $isAppend
     * @param int    $fileMode
     * @datetime 2019/8/30 18:30
     * @author roach
     * @email jhq0113@163.com
     */
    static public function write($fileName, $content, $isAppend= true, $fileMode = 0777)
    {
        $mode = $isAppend ? 'a' : 'w';
        if (($fp = @fopen($fileName, $mode)) === false) {
            return;
        }

        @fwrite($fp, $content);
        @fclose($fp);
        @chmod($fileName, $fileMode);
    }

    /**直接读文件
     * @param string  $fileName
     * @return string
     * @datetime 2019/8/30 18:30
     * @author roach
     * @email jhq0113@163.com
     */
    static public function read($fileName)
    {
        if(!file_exists($fileName)) {
            return '';
        }

        $fp = fopen($fileName,'r');
        if(!$fp) {
            return '';
        }

        $content = fread($fp,filesize($fileName));
        @fclose($fp);

        return $content;
    }

    /**用于读取大文件，一行一行读取
     * @param string $fileName
     * @return \Generator
     * @example
     *
     * foreach (EFile::getLines("file.txt") as $line) {
     *     echo $line;
     * }
     *
     * @datetime 2019/8/30 18:34
     * @author roach
     * @email jhq0113@163.com
     */
    static public function getLines($fileName)
    {
        $fp = @fopen($fileName, 'r');

        if(!$fp) {
            return;
        }

        try {
            while ($line = fgets($fp)) {
                yield $line;
            }
        } finally {
            @fclose($fp);
        }
    }
}