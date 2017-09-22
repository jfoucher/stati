#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Stati\Command\TestCommand;
use Symfony\Component\Console\Application;

$application = new Application('Stati', '@package_version@');
$application->add(new TestCommand());
$application->run();
