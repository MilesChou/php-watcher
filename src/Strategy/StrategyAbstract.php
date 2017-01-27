<?php

namespace Watcher\Strategy;

use Watcher\Logging\LoggerAwareTrait;

/**
 * Strategy interface
 */
abstract class StrategyAbstract
{
    use LoggerAwareTrait;

    /**
     * @param array $resources
     * @param callable $callable
     */
    abstract public function watch(array $resources, callable $callable);
}
