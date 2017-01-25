# Watcher

[![Travis CI](https://travis-ci.org/MilesChou/php-watcher.svg?branch=master)](https://travis-ci.org/MilesChou/php-watcher)

A simple watcher library written by PHP.

## Usage

Install using Composer

```
$ composer require mileschou/watcher
```

### Watch file

Example of watch file

```php
<?php

use Watcher\Watcher;

$watcher = new Watcher();
$watcher->setFile('file1', '/path/to/file');

$watcher->watch(function($alias, $file, $isInit) {
    if ($isInit) {
        return;
    }
    
    echo $file . ' is Changed';
});
```

### Run once

Example of run one time.

```php
<?php

use Watcher\Watcher;

$watcher = new Watcher();
$watcher->setFile('file1', '/path/to/file');

$watcher->run(function($alias, $file) {
    echo 'Show ' . $file;
});
```

### Container

You can using simple container, default will trans to ArrayObject
 
```php
<?php

use Watcher\Watcher;

$container = ['something'];

$watcher = new Watcher($container);
$watcher->setFile('file1', '/path/to/file');

$watcher->run(function($alias, $file) {
    /** @var ArrayObject $this */
    $data = $this->getArrayCopy();
    
    echo 'Container data is ' . $data[0]; // Will see 'something'
});
```

Or using other container library.
 
```php
<?php

use Watcher\Watcher;

$container = new \Pimple\Container();
$container['some-key'] = 'some-value';

$watcher = new Watcher($container);
$watcher->setFile('file1', '/path/to/file');

$watcher->run(function($alias, $file) {
    /** @var \Pimple\Container $this */
    $data = $this['some-key'];
    
    echo 'Pimple container data is ' . $data; // Will see 'some-value'
});
```
