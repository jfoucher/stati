<?php
/**
 * PageRendererTest.php
 *
 * Created By: jonathan
 * Date: 05/10/2017
 * Time: 01:21
 */

namespace Stati\Tests\Renderer;

use Stati\Entity\Page;
use Stati\Renderer\PageRenderer;
use PHPUnit\Framework\TestCase;
use Stati\Site\Site;
use Symfony\Component\Finder\SplFileInfo;

class PageRendererTest extends TestCase
{
    /**
     * @var Site
     */
    private $site;

    public function setUp()
    {
        $this->site = new Site([
            'permalink' => '/:year/',
            'includes_dir' => './',
            'layouts_dir' => __DIR__ . '/../fixtures/post_test'
        ]);
    }

    public function testPageRender()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/page.md', './', 'page.md');
        $page = new Page($file, $this->site);
        $this->site->addPage($page);
        $renderer = new PageRenderer($this->site);
        $pages = $renderer->renderAll();
        $this->assertEquals(count($pages), 1);
        $this->assertEquals($pages[0]->title, 'test page');
        $this->assertEquals($pages[0]->output, '<html><body><p>page</p></body></html>');
    }
}
