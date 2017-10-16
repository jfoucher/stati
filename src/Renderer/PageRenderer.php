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
