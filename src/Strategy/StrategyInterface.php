<?php

namespace Watcher\Strategy;

/**
 * Strategy interface
 */
interface StrategyInterface
{
    /**
     * @param array $resources
     * @param callable $callable
     */
    public function watch(array $resources, callable $callable);
}
