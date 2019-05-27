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

namespace Stati\Entity;

use Stati\Site\Site;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Parser\MarkdownParser;
use Liquid\Template;
use Liquid\Liquid;
use Stati\Liquid\Block\Highlight;
use Stati\Liquid\Tag\PostUrl;
use Liquid\Cache\File;
use Stati\Entity\Doc;

class Post extends Doc
{
    /**
     * Next post
     * @var Doc
     */
    protected $next;

    /**
     *  Previous post
     * @var Doc
     */
    protected $previous;

    /**
     *  Collection this post belongs to
     * @var Collection
     */
    protected $collection;

    /**
     * @return \Stati\Entity\Doc
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param \Stati\Entity\Doc $next
     */
    public function setNext($next)
    {
        $this->next = $next;
    }

    /**
     * @return \Stati\Entity\Doc
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param \Stati\Entity\Doc $previous
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param Collection $collection
     */
    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        if (isset($this->frontMatter['permalink'])) {
            return $this->frontMatter['permalink'];
        } elseif ($this->getCollection() && $this->getCollection()->getConfigItem('permalink')) {
            return $this->getCollection()->getConfigItem('permalink');
        }
        return $this->site->permalink;
    }


    public function getDate()
    {
        if ($this->date !== null) {
            return $this->date;
        }

        //try to get date from frontMatter
        $frontMatter = $this->getFrontMatter();
        if (isset($frontMatter['date'])) {
            if (is_int($frontMatter['date'])) {
                $this->date = date_create_from_format('U', $frontMatter['date']);
                return $this->date;
            }
            try {
                $this->date = new \DateTime($frontMatter['date']);
                return $this->date;
            } catch (\Exception $err) {
                // echo $err->getMessage()."\r\n";
            }
        }

        // Try to get date from filename
        try {
            $this->date = new \DateTime(substr($this->file->getBasename(), 0, 10));
            return $this->date;
        } catch (\Exception $err) {
        }

        return $this->date;
    }


    /**
     * Gets or generates the post excerpt
     * @return string
     */

    public function getExcerpt()
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        $frontMatter = $this->getFrontMatter();
        $content = $this->getContent();
        $siteConfig = $this->site->getConfig();

        if (isset($frontMatter['excerpt']) && $frontMatter['excerpt']) {
            $this->excerpt = $frontMatter['excerpt'];
            return $this->excerpt;
        }

        $separator = false;
        if (isset($frontMatter['excerpt_separator'])) {
            $separator = $frontMatter['excerpt_separator'];
        }
        if (!$separator && isset($siteConfig['excerpt_separator'])) {
            $separator = $this->site->getConfig()['excerpt_separator'];
        }
        if ($separator && strlen($separator) > 0 && strpos($content, $separator) !== false) {
            //We have a separator
            $ex = explode($separator, $content);
            $this->excerpt = $ex[0];
        }
        return $this->excerpt;
    }


    /**
     * @return string
     */
    public function getLayout()
    {
        $layout = parent::getLayout();
        if ($layout === null) {
            return isset($this->frontMatter['layout']) ? $this->frontMatter['layout'] : 'post';
        }
        return $layout;
    }
}
