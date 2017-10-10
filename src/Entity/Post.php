<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
}
