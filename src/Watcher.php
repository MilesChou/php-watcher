<?php
namespace Watcher;

use ArrayObject;
use Closure;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Watcher\Exception\FileNotFoundException;
use Watcher\Exception\InvalidContainerException;
use Watcher\Logging\LoggerAwareTrait;
use Watcher\Strategy\FileSystem;
use Watcher\Strategy\StrategyInterface;

/**
 * Watcher Class
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class Watcher implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ArrayObject|mixed
     */
    private $container;

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var StrategyInterface
     */
    private $strategy;

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
     * @return StrategyInterface
     */
    public function getStrategy()
    {
        if (null === $this->strategy) {
            $this->strategy = new FileSystem();
        }

        if (null !== $this->logger) {
            $this->strategy->setLogger($this->logger);
        }

        return $this->strategy;
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
     * @param StrategyInterface $strategy
     */
    public function setStrategy(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
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

        $strategy = $this->getStrategy();
        $strategy->watch($this->files, $callable);
    }
}
