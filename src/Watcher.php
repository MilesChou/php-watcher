<?php
namespace Watcher;

use Watcher\Exception\FileNotFoundException;

/**
 * Watcher Class
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class Watcher
{
    /**
     * @var array
     */
    private $fileList = [];

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
     * @param string $file
     */
    public function addFile($file)
    {
        if ($file{0} !== '/') {
            $file = getcwd() . '/' . $file;
        }

        if (!is_file($file)) {
            throw new FileNotFoundException("$file is not found.");
        }

        $this->fileList[] = $file;
    }

    /**
     * @param string[] $files
     */
    public function addFiles(array $files)
    {
        foreach ($files as $file) {
            $this->addFile($file);
        }
    }

    /**
     * Run watch
     *
     * @param callable $callback
     */
    public function watch(callable $callback)
    {
        // Initial callback state
        foreach ($this->fileList as $file) {
            $callback($file, true);
        }

        while (1) {
            clearstatcache();

            foreach ($this->fileList as $file) {
                if ($this->isChange($file)) {
                    $callback($file);
                }

                $this->updateModifyTime($file);
            }

            sleep(1);
        }
    }
}
