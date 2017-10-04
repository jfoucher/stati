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

use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Parser\MarkdownParser;
use Liquid\Template;
use Liquid\Liquid;

class Page extends Doc
{
    public function getPath()
    {
        $extension = $this->file->getExtension();
        if ($extension === 'md' || $extension === 'markdown' || $extension == 'mkd') {
            $extension = 'html';
        }
        $fname = pathinfo($this->file->getBasename(), PATHINFO_FILENAME);
        if (substr($fname, -5) === '.html') {
            $fname = pathinfo($fname, PATHINFO_FILENAME);
        }
        return str_replace('//', '/', '/'.$this->file->getRelativePath().'/'.$fname.'.'.$extension);
    }


    public function getDate()
    {
        if ($this->date !== null) {
            return $this->date;
        }

        //try to get date from frontMatter
        $frontMatter = $this->getFrontMatter();
        if (isset($frontMatter['date'])) {
            $dateString = $frontMatter['date'];
            try {
                $this->date = new \DateTime($dateString);
                return $this->date;
            } catch (\Exception $err) {
                // echo $err->getMessage()."\r\n";
            }
        }

        return $this->date;
    }
}
