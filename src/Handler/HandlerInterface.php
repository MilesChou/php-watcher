<?php

namespace Watcher\Handler;

/**
 * Handler interface
 *
 * @author MilesChou <jangconan@gmail.com>
 */
interface HandlerInterface
{
    /**
     * @param string $file
     * @param bool $isInit
     */
    public function invoke($file, $isInit);
}
