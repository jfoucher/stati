<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Composer\Script\Event;

class Install
{
    public static function postUpdate(Event $event)
    {
        self::postRemove();
        $fileSystem = new Filesystem();
        //Copy bin to /usr/local/bin
//        try {
//            $fileSystem->copy(__DIR__ . '/../build/stati.phar', '/usr/local/bin/stati');
//        } catch (IOException $e) {
//            echo $e->getMessage();
//        }
        // Copy bin to above vendor folder
        try {
            $fileSystem->copy(__DIR__ . '/../build/stati.phar', __DIR__ . '/../../../bin/stati');
        } catch (IOException $e) {
            echo $e->getMessage();
        }
    }

    public static function postRemove()
    {
        $fileSystem = new Filesystem();
        // Remove bin from /usr/local/bin
//        try {
//            $fileSystem->remove('/usr/local/bin/stati');
//        } catch (IOException $e) {
//            echo $e->getMessage();
//        }
        // remove bin from above vendor folder
        try {
            $fileSystem->remove(__DIR__ . '/../../../bin/stati');
        } catch (IOException $e) {
            echo $e->getMessage();
        }
    }
}
