<?php

namespace Watcher\Strategy;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Watcher\Exception\CannotUseInotifyException;
use Watcher\Util\System;

/**
 * Inotify strategy
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class Inotify extends StrategyAbstract implements LoggerAwareInterface
{
    /**
     * @var int
     */
    private $mask = IN_ATTRIB;

    /**
     * @var resource
     */
    private $fd;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * Inotify constructor.
     */
    public function __construct()
    {
        if (!function_exists('inotify_init')) {
            throw new CannotUseInotifyException('Extension inotify is not installed');
        }

        $this->log(LogLevel::INFO, "Inotify initial");
        $fd = inotify_init();

        if (false === $fd) {
            throw new CannotUseInotifyException('Inotify init error');
        }

        $this->fd = $fd;
    }

    /**
     * @param array $files
     * @param callable $callable
     */
    public function watch(array $files, callable $callable)
    {
        $this->log(LogLevel::INFO, "Inotify add watch");
        foreach ($files as $alias => $file) {
            $identity = inotify_add_watch($this->fd, $file, $this->mask);
            $this->registry[$identity] = [
                'alias' => $alias,
                'file' => $file,
            ];
        }

        while (1) {
            $this->log(LogLevel::DEBUG, "Inotify read. Memory usage: " . System::getMemoryUsage());
            $events = inotify_read($this->fd);
            if ($events) {
                foreach ($events as $event) {
                    $identity = $event['wd'];
                    list($alias, $file) = $this->registry[$identity];
                    $this->log(LogLevel::INFO, "$file is changed, do callable");
                    $callable($alias, $file);
                }
            }
        }
    }
}
