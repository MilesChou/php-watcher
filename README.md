# Watcher

[![Travis CI](https://travis-ci.org/MilesChou/php-watcher.svg?branch=master)](https://travis-ci.org/MilesChou/php-watcher)

A simple watcher library written by PHP.

## Usage

Install using Composer

```
$ composer require mileschou/watcher
```

And write the code

```php
<?php

use Watcher\Watcher;

$watcher = new Watcher();
$watcher->addFile('/path/to/file');

$watcher->watch(function($file, $isInit) {
    if ($isInit) {
        return;
    }
    
    echo $file . ' is Changed';
});
```
