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

use Liquid\LiquidException;
use Stati\Entity\Page;
use Stati\Entity\Sass;
use Stati\Event\ConsoleOutputEvent;
use Stati\Site\SiteEvents;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Render Pages, i.e. documents with frontmatter but that are not part of a collection
 * Class PageRenderer
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
            try {
                $pages[] = $this->render($page);
            } catch (LiquidException $err) {
                $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('error', [['Could not render page'.$page->getTitle() . ' because of the following error', $err->getMessage()]]));
            }
        }
        return $pages;
    }
}
