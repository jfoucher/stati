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

namespace Stati\Tests\Entity;

use Stati\Entity\StaticFile;
use Stati\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class StaticFileTest extends TestCase
{
    public function testGetter()
    {
        $file = new StaticFile(__DIR__ . '/../fixtures/static/static.txt', 'static/', 'static/static.txt');
        $this->assertEquals('static/static.txt', $file->path);
        $this->assertEquals('static', $file->basename);
        $this->assertEquals('static.txt', $file->name);
        $this->assertEquals('.txt', $file->extname);
        $this->assertEquals(null, $file->none);
        $this->assertEquals(date_create_from_format('U', (string)$file->getMTime())->format(DATE_RFC3339), $file->modified_time);
    }

    public function testFieldExists()
    {
        $file = new StaticFile(__DIR__ . '/../fixtures/static/static.txt', 'static/', 'static/static.txt');

        $items = ['path', 'modified_time', 'name', 'basename', 'extname'];

        foreach ($items as $item) {
            $this->assertTrue($file->field_exists($item));
        }
        $this->assertFalse($file->field_exists('none'));
    }
}
