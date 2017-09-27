<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Plugin\Paginator\Renderer;

use Stati\Entity\Doc;
use Stati\Plugin\Paginator\Entity\PaginatorPage;
use Stati\Renderer\Renderer;
use Symfony\Component\Finder\Finder;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class PaginatorRenderer extends Renderer
{
    public function renderAll()
    {
        /**
         * @var \Stati\Plugin\Paginator\Entity\Paginator $paginator
         */
        $paginator = $this->site->paginator;

        $pages = [];

        $finder = new Finder();
        $fileIterator = $finder->in('./')
            ->name('/^index.html$/')
            ->depth('== 0')->getIterator();
        $files = iterator_to_array($fileIterator);
        $file = $files['./index.html'];

        while ($paginator->next_page) {
            // This is here to avoid rendering first page, which is already rendered in the site index
            $paginator->setPage($paginator->getPage() + 1);
            // $file is index.html
            $currentPage = $paginator->getPage();
            $currentPagePath = $paginator->current_page_path;
            $page = new PaginatorPage($file, $this->site, $currentPage, $currentPagePath);
            $rendered = $this->render($page);
            $pages[] = $rendered;
        }
        $paginator->setPage(1);

        return $pages;
    }
}