<?php

namespace Watcher\Handler;

/**
 * Handler abstract, it's callable too.
 *
 * @author MilesChou <jangconan@gmail.com>
 */
abstract class HandlerAbstract implements HandlerInterface
{
    /**
     * @param $alias
     * @param string $file
     * @param bool $isInit
     */
    public function __invoke($alias, $file, $isInit = false)
    {
        $this->invoke($alias, $file, $isInit);
    }
}
