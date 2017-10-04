<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $this->site);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<p>c</p>', $doc->getOutput());
    }

    public function testRenderDocWithLayout()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-13-with-layout.markdown', './', '2017-08-13-with-layout.markdown');
        $doc = new Doc($file, $this->site);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<html><body><p>c</p></body></html>', $doc->getOutput());
    }

    /**
     * @expectedException \Stati\Exception\FileNotFoundException
     */
    public function testRenderDocWithLayoutNotFound()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-13-with-layout.markdown', './', '2017-08-13-with-layout.markdown');
        $doc = new Doc($file, $this->site);
        $frontMatter = $doc->getFrontMatter();
        $frontMatter['layout'] = 'wrong-layout';
        $doc->setFrontMatter($frontMatter);
        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
    }


    public function testRenderDocWithLayouts()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-13-with-2-layouts.markdown', './', '2017-08-13-with-2-layouts.markdown');
        $doc = new Doc($file, $this->site);

        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals('<html><body><article><p>c</p></article></body></html>', $doc->getOutput());
    }

    /**
     * @expectedException \Liquid\LiquidException
     */
    public function testRenderDocWithIncorrectSyntaxLayouts()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-13-with-incorrect-layout.markdown', './', '2017-08-13-with-incorrect-layout.markdown');
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
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-13-with-incorrect-layout.markdown', './', '2017-08-13-with-incorrect-layout.markdown');
        $doc = new Doc($file, $this->site);

        $renderer = new Renderer($this->site);
        $doc = $renderer->render($doc);
        $this->assertEquals(null, $doc->getOutput());
    }
}
