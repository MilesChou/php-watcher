<?php

namespace Watcher\Exception;

/**
 * Can not use inotify exception
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class CannotUseInotifyException extends \InvalidArgumentException implements WatcherException
{

}
