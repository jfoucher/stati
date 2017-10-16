<?php

/*
 * This file is part of the Stati package.
 *
 * Copyright 2017 Jonathan Foucher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
