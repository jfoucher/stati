<?php


namespace Stati;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class Install
{
    public static function postUpdate()
    {
        $fileSystem = new Filesystem();
        try {
            $fileSystem->copy('./build/stati.phar', '../bin/');
        } catch(IOException $e) {
            echo $e->getMessage();
        }

    }
}