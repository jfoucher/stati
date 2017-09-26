<?php
/**
 * Post.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 23:05
 */

namespace Stati\Entity;

use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Parser\MarkdownParser;
use Liquid\Template;
use Liquid\Liquid;

class Page extends Post
{

    public function getPath()
    {
        $extension = $this->file->getExtension();
        if ($extension === 'md' || $extension === 'markdown' || $extension == 'mkd') {
            $extension = 'html';
        }
        return str_replace('//', '/','/'.$this->file->getRelativePath().'/'.pathinfo($this->file->getBasename(), PATHINFO_FILENAME).'.'.$extension);
    }
}