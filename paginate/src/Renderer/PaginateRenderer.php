<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Plugin\Paginate\Renderer;

use Stati\Entity\Doc;
use Stati\Plugin\Paginate\Entity\PaginatorPage;
use Stati\Renderer\Renderer;
use Symfony\Component\Finder\Finder;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class PaginateRenderer extends Renderer
{
    public function renderAll()
    {
        /**
         * @var \Stati\Plugin\Paginate\Entity\Paginator $paginator
         */
        $paginator = $this->site->paginator;

        $pages = [];

        $finder = new Finder();
        $finder->in('./')
            ->name('index.html')
            ->depth(' >=0')
//            ->contains('/\{\{[\s]*paginator')
            ->contains('/---\s+(.*)\s+---\s+/s')
            ;

        foreach ($finder as $file) {
            while (count($paginator->getPosts()) > 0) {
                // $file is index.html
                $currentPage = $paginator->getPage();
                $currentPagePath = $paginator->current_page_path;
                $page = new PaginatorPage($file, $this->site, $currentPage, $currentPagePath);
                $rendered = $this->render($page);
                $pages[] = $rendered;
                // We now render the first page in /index.html, so this moves here
                $paginator->setPage($paginator->getPage() + 1);
            }
            $paginator->setPage(1);
        }

        return $pages;
    }
}
