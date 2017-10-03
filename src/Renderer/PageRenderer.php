<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
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
