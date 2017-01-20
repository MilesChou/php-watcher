<?php

namespace Watcher\Util;

/**
 * File Util
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class File
{
    /**
     * @param string $file
     * @param int $buffer
     * @return int
     */
    public static function getTotalLines($file, $buffer = 1048576)
    {
        $fp = fopen($file, "r");
        $sumNb = 0;
        while (!feof($fp)) {
            if ($data = fread($fp, $buffer)) {
                $num = substr_count($data, "\n");
                $sumNb += $num;
            }
        }

        fclose($fp);

        return $sumNb;
    }
}
