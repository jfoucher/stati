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

namespace Stati\Tests\Renderer;

use Stati\Entity\Page;
use Stati\Renderer\PageRenderer;
use Stati\Tests\TestCase;
use Stati\Site\Site;
use Symfony\Component\Finder\SplFileInfo;

class PageRendererTest extends TestCase
{
    public function testPageRender()
    {
        $file = $this->getFile('page.md');
        $page = new Page($file, $this->site);
        $this->site->addPage($page);
        $renderer = new PageRenderer($this->site);
        $pages = $renderer->renderAll();
        $this->assertEquals(count($pages), 1);
        $this->assertEquals($pages[0]->title, 'test page');
        $this->assertEquals($pages[0]->output, '<html><body><p>page</p></body></html>
');
    }
}
