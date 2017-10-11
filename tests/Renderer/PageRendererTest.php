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
