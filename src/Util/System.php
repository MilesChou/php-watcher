<?php

namespace Watcher\Util;

/**
 * System Class
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class System
{
    /**
     * @return string
     * @see http://php.net/manual/zh/function.memory-get-usage.php
     */
    public static function getMemoryUsage()
    {
        $size = memory_get_usage(true);
        $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $i = (int)floor(log($size, 1024));
        return @round($size / pow(1024, $i), 2) . ' ' . $unit[$i];
    }
}
