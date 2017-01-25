<?php

namespace Watcher\Strategy;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Watcher\Logging\LoggerAwareTrait;
use Watcher\Util\System;

/**
 * Polling strategy
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class FileSystem implements StrategyInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    private $modifyTime = [];

    /**
     * @param string $file
     * @return bool
     */
    private function isChange($file)
    {
        if (!isset($this->modifyTime[$file])) {
            return false;
        }

        $modifyTime = filemtime($file);

        return $this->modifyTime[$file] !== $modifyTime;
    }

    /**
     * @param string $file
     */
    private function updateModifyTime($file)
    {
        $this->modifyTime[$file] = filemtime($file);
    }

    /**
     * @param array $files
     * @param callable $callable
     */
    public function watch(array $files, callable $callable)
    {
        while (1) {
            $this->log(LogLevel::DEBUG, "Clear file cache. Memory usage: " . System::getMemoryUsage());
            clearstatcache();

            foreach ($files as $alias => $file) {
                if ($this->isChange($file)) {
                    $this->log(LogLevel::INFO, "$file is changed, do callable");
                    $callable($alias, $file);
                }

                $this->updateModifyTime($file);
            }

            sleep(1);
        }
    }
}
