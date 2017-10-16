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

use Stati\Exception\FileNotFoundException;
use Stati\Renderer\Renderer;
use Stati\Tests\TestCase;
use Stati\Site\Site;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Entity\Doc;

class RendererTest extends TestCase
{
    public function testCreateRenderer()
    {
        $renderer = new Renderer($this->site);
        $this->assertEquals($renderer->getSite()->permalink, $this->site->getConfig()['permalink']);
        $this->assertEquals($renderer->getSite()->includes_dir, $this->site->getConfig()['includes_dir']);
    }

    public function testRenderDocWithoutLayout()
    {
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $this->site);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<p>c</p>', $doc->getOutput());
    }

    public function testRenderDocWithLayout()
    {
        $file = $this->getFile('2017-08-13-with-layout.markdown');
        $doc = new Doc($file, $this->site);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<html><body><p>c</p></body></html>
', $doc->getOutput());
    }

    public function testRenderDocWithLayoutNotFound()
    {
        $file = $this->getFile('2017-08-13-with-layout.markdown');
        $doc = new Doc($file, $this->site);
        $frontMatter = $doc->getFrontMatter();
        $frontMatter['layout'] = 'wrong-layout';
        $doc->setFrontMatter($frontMatter);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<p>c</p>', $doc->getOutput());
    }


    public function testRenderDocWithLayouts()
    {
        $file = $this->getFile('2017-08-13-with-2-layouts.markdown');
        $doc = new Doc($file, $this->site);

        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<html><body><article><p>c</p></article></body></html>
', $doc->getOutput());
    }

    /**
     * @expectedException \Liquid\LiquidException
     */
    public function testRenderDocWithIncorrectSyntaxLayouts()
    {
        $file = $this->getFile('2017-08-13-with-incorrect-layout.markdown');
        $doc = new Doc($file, $this->site);

        $frontMatter = $doc->getFrontMatter();
        $frontMatter['layout'] = 'incorrect-syntax-layout-layout';
        $doc->setFrontMatter($frontMatter);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals(null, $doc->getOutput());
    }

    /**
     * @expectedException \Liquid\LiquidException
     */
    public function testRenderDocWithIncorrectSyntaxLayout()
    {
        $file = $this->getFile('2017-08-13-with-incorrect-layout.markdown');
        $doc = new Doc($file, $this->site);

        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals(null, $doc->getOutput());
    }
}
