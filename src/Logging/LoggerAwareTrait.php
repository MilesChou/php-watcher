<?php

namespace Watcher\Logging;

use Psr\Log\LoggerAwareTrait as PsrLoggerAwareTrait;

trait LoggerAwareTrait
{
    use PsrLoggerAwareTrait;

    /**
     * @param $level
     * @param $msg
     */
    public function log($level, $msg)
    {
        if (null !== $this->logger) {
            $this->logger->log($level, $msg);
        }
    }
}
