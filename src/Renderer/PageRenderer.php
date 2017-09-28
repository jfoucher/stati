<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Renderer;

use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class PageRenderer extends Renderer
{
    public function renderAll()
    {
        $pages = [];
        foreach ($this->site->getPages() as $page) {
            /**
             * @var Page $page
             */
            $page->setSite($this->site);
            $pages[] = $this->render($page);
        }
        return $pages;
    }
}
