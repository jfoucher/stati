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
use Liquid\Liquid;

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
        $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('section', ['Rendering pages', OutputInterface::VERBOSITY_VERBOSE]));
        foreach ($this->site->getPages() as $page) {
            /**
             * @var Page $page
             */
            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Rendering page '.$page->getSlug(), OutputInterface::VERBOSITY_DEBUG]));
            $page->setSite($this->site);
            try {
                $pages[] = $this->render($page);
            } catch (LiquidException $err) {
                $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('error', [['Could not render page'.$page->getTitle() . ' because of the following error', $err->getMessage()]]));
            }
        }
        $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Rendered ' . count($pages) . ' pages'], OutputInterface::VERBOSITY_DEBUG));
        return $pages;
    }
}
