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
     * @param string $file
     * @param bool $inInit
     */
    public function __invoke($file, $inInit = false)
    {
        $this->invoke($file, $inInit);
    }
}
