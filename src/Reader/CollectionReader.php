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

namespace Stati\Reader;

use Stati\Event\ConsoleOutputEvent;
use Stati\Site\SiteEvents;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Stati\Entity\Post;
use Stati\Entity\Collection;

/**
 * Renders collection defined in _config.yml
 * Class CollectionRenderer
 * @package Stati\Renderer
 */
class CollectionReader extends Reader
{
    public function read()
    {
        $config = $this->site->getConfig();
        if (!isset($config['collections']) || !is_array($config['collections'])) {
            return null;
        }

        $collections = [];
        foreach ($config['collections'] as $collectionName => $collectionData) {
            if (is_int($collectionName) && is_string($collectionData)) {
                $collectionName = $collectionData;
                $collectionData = [];
            }

            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('section', ['Reading collection '.$collectionName]));

            $collection = new Collection($collectionName, $collectionData);
            $finder = new Finder();
            $source = $config['source'];
            if (strpos($source, './') === 0) {
                $source = substr($source, 2);
            }
            $finder
                ->in($source . '/')
                ->path(sprintf('/_%s/', $collectionName))
                ->files()
                ->name('/(.md|.mkd|.markdown)$/')
            ;

            foreach ($config['exclude'] as $exclude) {
                if (strpos($exclude, '/') === 0) {
                    $exclude = substr($exclude, 1);
                }
                if (strpos($exclude, '*') !== false) {
                    //If pattern is a glob, treat as such
                    $finder->notName($exclude);
                    $finder->notPath($exclude);
                } else {
                    // Otherwise, match start and end of string
                    $finder->notName('|^'.$exclude.'$|');
                    $finder->notPath('|^'.$exclude.'|');
                }
            }

            $posts = [];

            foreach ($finder as $file) {
                $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Reading file '.$file->getRelativePathname()], OutputInterface::VERBOSITY_DEBUG));
                $post = new Post($file, $this->site);
                if ($post->getDate() && $post->getDate()->getTimestamp() >= date_create()->getTimestamp() && $post->published === false) {
                    continue;
                }
                $post->setCollection($collection);
                $posts[] = $post;
            }

            usort($posts, function ($a, $b) {
                if (!$a->getDate() || !$b->getDate()) {
                    return 0;
                }
                return $a->getDate()->getTimestamp() > $b->getDate()->getTimestamp() ? -1 : 1;
            });

            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', [$collection->getLabel().' has '.count($posts).' documents.'], OutputInterface::VERBOSITY_VERY_VERBOSE));

            $collection->setDocs($posts);
            $collections[$collectionName] = $collection;
        }

        return $collections;
    }
}
