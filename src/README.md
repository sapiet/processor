# Processor

This package allows you to handle state modifications.

For example, when you have a cron job that is executed every minute, and you want to alert someone if your script does not work, but once, not every minute ! 


## Installation

`composer require sapiet/processor`


## Usage

This example simulates a script that start throwing exceptions every second.

This configuration allows you to do something every 4 seconds.

If the `Processor::ERROR_CALLBACK_DELAY_OPTION` option is ommited, the callback will be called once.

In this example, the last call is executed without throwing an exception, so the success callback is called

```php
<?php

require 'vendor/autoload.php';

use Sapiet\Processor\Processor;
use Sapiet\Processor\Storage\FileStorage;

$processor = (new Processor())
    ->withProcess(function (bool $fail, int $sleep = 1) {
        sleep($sleep);
        if (true === $fail) {
            throw new \Exception('failed!');
        }
    })
    ->withStorage(new FileStorage('processor.txt'))
    ->withOption(Processor::ERROR_CALLBACK_DELAY_OPTION, 4)
    ->onSuccess(function() {
        dump('Yeah!');
    })
    ->onError(function (\Exception $exception) {
        dump(sprintf('Oh noooo (%s)', $exception->getMessage()));
    })
;

$values = array_merge(
    [false],
    array_fill(0, 10, true),
    [false]
);

foreach ($values as $value) {
    $processor->process($value);
}

```

Ouput:

```
"Oh noooo (failed!)"
"Oh noooo (failed!)"
"Oh noooo (failed!)"
"Yeah!"

```
