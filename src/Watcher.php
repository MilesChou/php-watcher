<?php
namespace Watcher;

use ArrayObject;
use Closure;
use Psr\Log\LogLevel;
use Watcher\Exception\FileNotFoundException;
use Watcher\Exception\InvalidContainerException;
use Watcher\Logging\LoggerAwareTrait;
use Watcher\Util\System;

/**
 * Watcher Class
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class Watcher
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var ArrayObject|mixed
     */
    private $container;

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
     * @param array|ArrayObject $container
     */
    public function __construct($container = [])
    {
        if (!is_object($container) && !is_array($container)) {
            throw new InvalidContainerException('Container parameter is invalid, please use array / object type');
        }

        if (is_array($container)) {
            $container = new ArrayObject($container);
        }

        $this->container = $container;
    }

    /**
     * @return ArrayObject|mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Run once
     *
     * @param callable $callable
     */
    public function run(callable $callable)
    {
        if ($callable instanceof Closure) {
            $this->log(LogLevel::INFO, 'Bind container to callable');
            $callable = $callable->bindTo($this->container);
        }

        $this->log(LogLevel::INFO, 'Run once time');
        foreach ($this->files as $alias => $file) {
            $this->log(LogLevel::INFO, "Do callable by $file");
            $callable($alias, $file);
        }
    }

    /**
     * @param string $alias
     * @param string $file
     */
    public function setFile($alias, $file)
    {
        if ($file{0} !== '/') {
            $file = getcwd() . '/' . $file;
        }

        if (!is_file($file)) {
            throw new FileNotFoundException("$file is not found.");
        }

        $this->files[$alias] = $file;
    }

    /**
     * @param string[] $files
     */
    public function setFiles(array $files)
    {
        foreach ($files as $alias => $file) {
            $this->setFile($alias, $file);
        }
    }

    /**
     * Run and watch
     *
     * @param callable $callable
     */
    public function watch(callable $callable)
    {
        if ($callable instanceof Closure) {
            $this->log(LogLevel::INFO, 'Bind container to callable');
            $callable = $callable->bindTo($this->container);
        }

        $this->log(LogLevel::INFO, 'Initial callable state');
        foreach ($this->files as $alias => $file) {
            $callable($alias, $file, true);
        }

        $this->log(LogLevel::INFO, 'Start loop');
        while (1) {
            $this->log(LogLevel::DEBUG, "Clear file cache. Memory usage: " . System::getMemoryUsage());
            clearstatcache();

            foreach ($this->files as $alias => $file) {
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
