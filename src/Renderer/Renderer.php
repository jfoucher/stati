<?php
/**
 * Renderer.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 23:08
 */

namespace Stati\Renderer;


use Stati\Entity\Doc;
use Stati\Site\Site;
use Stati\Entity\Post;
use Liquid\Liquid;
use Stati\Exception\FileNotFoundException;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Liquid\Template;
use Liquid\Cache\File;
use Stati\Liquid\Block\Highlight;
use Stati\Liquid\Tag\PostUrl;

class Renderer
{
    /**
     * @var Site
     */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }


    protected function render(Doc $doc)
    {
        Liquid::set('INCLUDE_ALLOW_EXT', true);
        Liquid::set('INCLUDE_PREFIX', './_includes/');
        Liquid::set('HAS_PROPERTY_METHOD', 'get');
        $frontMatter = $doc->getFrontMatter();
        $content = $doc->getContent();

        //If we have a layout
        if (isset($frontMatter['layout'])) {
            $config = [
                'content' => $content,
                'page' => $doc,
                'post' => $doc,
                'site' => $this->site,
            ];

            if (isset($this->site->paginator)) {
                $config['paginator'] = $this->site->paginator;
            }

            try {
                $content = $this->renderWithLayout($frontMatter['layout'], $config);
            } catch (FileNotFoundException $err) {
                throw new FileNotFoundException($err->getMessage(). ' for post "'.$doc->getTitle().'"');
            }
        }

        $doc->setOutput($content);
        return $doc;
    }

    protected function renderWithLayout($layoutFile, $config)
    {
        $layout = @file_get_contents('./_layouts/'.$layoutFile.'.html');
        if (!$layout) {
            throw new FileNotFoundException('Layout file "'.$layoutFile.'" not found in layout folder');
        }
        $layoutFrontMatter = FrontMatterParser::parse($layout);
        $layoutContent = ContentParser::parse($layout);

        if (isset($layoutFrontMatter['layout'])) {
            $template = new Template('./_includes/'/*, new File(['cache_dir' => '/tmp/'])*/);
            $template->registerTag('highlight', Highlight::class);
            $template->registerTag('post_url', PostUrl::class);
            $template->parse($layoutContent);
            $config['content'] = $template->render($config);
            return $this->renderWithLayout($layoutFrontMatter['layout'], $config);
        }

        $template = new Template('./_includes/'/*, new File(['cache_dir' => '/tmp/'])*/);
        $template->registerTag('highlight', Highlight::class);
        $template->registerTag('post_url', PostUrl::class);
        $template->parse($layoutContent);
        return $template->render($config);
    }

}