<?php


namespace Stati;

use Symfony\Component\Filesystem\Filesystem;

class Install
{
    public function postUpdate()
    {
        $fileSystem = new Filesystem();
        $fileSystem->copy('./build/stati.phar', '../bin/');
    }
}