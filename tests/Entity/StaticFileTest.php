<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        $this->assertEquals(date_create_from_format('U', $file->getMTime())->format(DATE_RFC3339), $file->modified_time);
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
