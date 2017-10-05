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
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.md', 'post_test/', 'page.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('/post_test/page.html', $doc->getPath());
    }

    public function testPathForHtmlMdFile()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.html.md', 'post_test/', 'page.html.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('/post_test/page.html', $doc->getPath());
    }

    public function testContentForHtmlMdFile()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.html.md', 'post_test/', 'page.html.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('<p>html</p>', $doc->getContent());
    }

    public function testContent()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.md', 'post_test/', 'page.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('<p>page</p>', $doc->getContent());
    }

    public function testNoDateShouldReturnNull()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.md', 'post_test/', 'page.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals(null, $doc->date);
    }

    public function testWrongDateShouldReturnNull()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.html.md', 'post_test/', 'page.html.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals(null, $doc->date);
    }

    public function testDate()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page-with-date.md', 'post_test/', 'page-with-date.md');
        $doc = new Page($file, $this->site);
        $this->assertEquals('2011-09-22T01:09:13+02:00', $doc->date);
    }
}
