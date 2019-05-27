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

use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Parser\MarkdownParser;
use Liquid\Template;
use Liquid\Liquid;

class Page extends Doc
{
    public function getOutputPath()
    {
        if ($this->path) {
            return $this->path;
        }
        $frontMatter = $this->getFrontMatter();

        $extension = $this->file->getExtension();
        $allowedExtensions = explode(',', $this->site->getConfig()['markdown_ext']);
        if (in_array($extension, $allowedExtensions)) {
            $extension = 'html';
        }
        $fname = pathinfo($this->file->getBasename(), PATHINFO_FILENAME);
        if (isset($frontMatter['permalink'])) {
            $fname = $frontMatter['permalink'];
        }
        if (substr($fname, -5) === '.html') {
            $fname = pathinfo($fname, PATHINFO_FILENAME);
        }
        if (substr($fname, -1) === '/') {
            $fname .= 'index';
        }

        $this->path = preg_replace('|/+|', '/', '/'.$this->file->getRelativePath().'/'.$fname.'.'.$extension);
        return $this->path;
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
            }
        }

        return $this->date;
    }
}
