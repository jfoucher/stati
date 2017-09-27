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
class PaginatorRenderer extends Renderer
{
    public function renderAll()
    {
        /**
         * @var \Stati\Plugin\Paginator\Entity\Paginator $paginator
         */
        $paginator = $this->site->paginator;

        $posts = [];

        foreach ($paginator->getAllPosts() as $post) {
            /**
             * @var Doc $post
             */
            $post->setSite($this->site);
            $posts[] = $this->render($post);
        }

        $paginator->setPosts($posts);
        return $paginator;
    }
}