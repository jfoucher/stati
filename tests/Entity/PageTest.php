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

use Stati\Entity\Page;
use Stati\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class PageTest extends TestCase
{
    public function testPath()
    {
        $file = $this->getFile('page.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('/page.html', $doc->getPath());
    }

    public function testPathForHtmlMdFile()
    {
        $file = $this->getFile('page.html.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('/page.html', $doc->getPath());
    }

    public function testContentForHtmlMdFile()
    {
        $file = $this->getFile('page.html.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('<p>html</p>', $doc->getContent());
    }

    public function testContent()
    {
        $file = $this->getFile('page.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('<p>page</p>', $doc->getContent());
    }

    public function testNoDateShouldReturnNull()
    {
        $file = $this->getFile('page.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals(null, $doc->date);
    }

    public function testWrongDateShouldReturnNull()
    {
        $file = $this->getFile('page.html.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals(null, $doc->date);
    }

    public function testDate()
    {
        $file = $this->getFile('page-with-date.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('2011-09-22T01:09:13+02:00', $doc->date);
    }
}
