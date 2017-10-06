<?php


namespace Stati;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Composer\Script\Event;

class Install
{
    public static function postUpdate(Event $event)
    {
        $fileSystem = new Filesystem();
        //Copy bin to /usr/local/bin
        try {
            $fileSystem->copy(__DIR__ . '/../build/stati.phar', '/usr/local/bin/stati');
        } catch(IOException $e) {
            echo $e->getMessage();
        }
        // Copy bin to above vendor folder
        try {
            $fileSystem->copy(__DIR__ . '/../build/stati.phar', __DIR__ . '/../../../bin/stati');
        } catch(IOException $e) {
            echo $e->getMessage();
        }

    }
}