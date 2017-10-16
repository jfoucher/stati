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
use Stati\Entity\Collection;
use Stati\Entity\Post;
use Stati\Site\SiteEvents;
use Stati\Event\ConsoleOutputEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Liquid\Liquid;

/**
 * Renders documents that are part of a collection
 * Class CollectionRenderer
 * @package Stati\Renderer
 */
class CollectionRenderer extends Renderer
{
    public function renderAll()
    {
        $collections = [];
        foreach ($this->site->getCollections() as $collection) {
            /**
             * @var Collection $collection
             */

            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('section', ['Rendering collection '.$collection->getLabel()]));

            $docs = [];
            $collectionDocs = $collection->getDocs();

            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', [$collection->getLabel().' contains '.count($collectionDocs). ' documents'], OutputInterface::VERBOSITY_VERY_VERBOSE));

            foreach ($collectionDocs as $k => $doc) {
                /**
                 * @var Post $doc
                 */
                $doc->setSite($this->site);
                $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Rendering file '.$doc->getSlug()], OutputInterface::VERBOSITY_DEBUG));
                if ($collection->getLabel() === 'posts') {
                    if ($k > 0) {
                        $doc->setNext($collectionDocs[$k - 1]);
                    }
                    if ($k < count($collectionDocs) -1) {
                        $doc->setPrevious($collectionDocs[$k + 1]);
                    }
                }
                $this->cacheFileName = 'rendered-' . $doc->getSlug() . '-' . sha1(implode('', Liquid::arrayFlatten($doc->getFrontMatter())) . $doc->getContent() . $this->site . implode('', Liquid::arrayFlatten($this->site->getConfig())));

                try {
                    if ($collection->getConfigItem('output')) {
                        $docs[] = $this->render($doc);
                    } else {
                        $doc->setOutput($doc->getContent());
                        $docs[] = $doc;
                    }
                } catch (LiquidException $err) {
                    $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('error', [['Could not render ' . $collection->getLabel() .' ' . $doc->getTitle() . ' because of the following error', $err->getMessage()]]));
                }
            }

            $collection->setDocs($docs);

            $collections[$collection->getLabel()] = $collection;
        }
        return $collections;
    }
}
