<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Renderer;

use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Stati\Liquid\Template;
use Liquid\Liquid;
use Stati\Parser\MarkdownParser;
use Stati\Link\Generator;
use Stati\Liquid\Block\Highlight;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Liquid\Tag\PostUrl;
use Liquid\Cache\File;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class FilesRenderer extends PostsRenderer
{
    public function render()
    {
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in('./')
            ->notPath('/^_/')
            ->files()
            ->notName('/^_/')
            ->notPath('node_modules')
//            ->notName('index.html')
            ->contains('/---\s+(.*)\s+---\s+/s');
        if($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
            $pb = $this->style->createProgressBar($finder->count());
        }

        $files = [];
        foreach ($finder as $file) {
            if($this->style->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->style->text($file->getRelativePathname());
            }
            if ($file->getExtension() === "scss" || $file->getExtension() === "sass") {
                $page = new Sass($file, $this->config);
            } else {
                $page = new Page($file, $this->config);
            }

            $rendered = $this->renderPage($page);

            $dir = str_replace('//', '/', './_site/'.pathinfo($page->getPath(),PATHINFO_DIRNAME));
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents(str_replace('//', '/', './_site/'.$page->getPath()), $rendered);
            if($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
                $pb->advance();
            }
            $files[] = $page;
        }
        if($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
            $pb->finish();
        }
        return $files;
    }
}