<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Renderer;

use Stati\Entity\PaginatorPage;
use Symfony\Component\Finder\Finder;
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
class PaginatorRenderer extends PostsRenderer
{
    public function render()
    {
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in('./')
            ->files()
            ->notPath('node_modules')
            ->notPath('/^_/')
            ->name('index.html')
//            ->contains('/\{(\{|%){1}\s*paginator([^}])*(\}|%){1}\}/')
        ;
        if($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
            $pb = $this->style->createProgressBar($finder->count());
        }

        foreach ($finder as $file) {
            $this->style->text($file->getRelativePathname());
            if ($this->style->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->style->text($file->getRelativePathname());
            }


            if (isset($this->config['paginator'])) {
                $paginator = $this->config['paginator'];
                while ($paginator->next_page) {
                    $paginator->setPage($paginator->getPage() + 1);
                    $page = new PaginatorPage($file, array_merge($this->config, ['paginator' => $paginator]));
                    $rendered = $this->renderPage($page);
                    $dir = str_replace('//', '/', './_site/'.pathinfo($page->getPath(),PATHINFO_DIRNAME));
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    file_put_contents(str_replace('//', '/', './_site/'.$page->getPath()), $rendered);
                    if($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
                        $pb->advance();
                    }
                }
            }
        }
        if ($this->style->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
            $pb->finish();
        }
        return count($finder);
    }
}