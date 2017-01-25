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
     * @param $alias
     * @param string $file
     * @param bool $isInit
     */
    public function invoke($alias, $file, $isInit);
}
