<?php
/**
 * DirectoryRenderer.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 00:43
 */

namespace Stati\Renderer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;
use Stati\Renderer\PostsRenderer;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryRenderer extends PostsRenderer
{
    public function render()
    {
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in('./')
            ->depth('== 0')
            ->notPath('/^_/')
            ->directories()
            ->notName('/^_/');
        if($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
            $pb = $this->style->createProgressBar($finder->count());
        }
        foreach ($finder as $file) {
            $this->style->text($file->getRelativePathname());
            $fs = new Filesystem();
            $fs->mirror('./'.$file->getRelativePathname(), './_site/'.$file->getRelativePathname());
        }
    }
}