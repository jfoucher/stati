<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Renderer;

use Liquid\Cache\File;
use Stati\Pygments\Pygments;
use Stati\Entity\Post;
use Stati\Exception\FileNotFoundException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Liquid\Template;
use Liquid\Liquid;
use Stati\Liquid\Block\Highlight;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Liquid\Tag\PostUrl;

class PostsRenderer
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var SymfonyStyle
     */
    protected $style;

    protected $pygments;

    public function __construct(array $config, SymfonyStyle $style = null)
    {
        $this->config = $config;
        $this->style = $style;
        $this->pygments = new Pygments();
    }

    public function render()
    {
        // Get top level files and parse
        $finder = new Finder();
        $finder->depth(' <= 1')
        ->files()
        ->in('./_posts/')
        ->name('/(\.mkd|\.md|\.markdown)$/');
        //            ->name('/\.md$/');

        $posts = [];
        $pb = $this->style->createProgressBar($finder->count());

        foreach ($finder as $file) {
            $time = microtime(true);
            $_SERVER['pygments'] = $this->pygments;
            $post = new Post($file, $this->config);
            $posts[] = $post;
            //Put rendered file in _site directory, in it's right place
            $dir = './_site'.pathinfo($post->getPath(),PATHINFO_DIRNAME);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $content = $this->renderPage($post);

            file_put_contents('./_site'.$post->getPath(), $content);
//            $this->style->text($post->getSlug().' generated in '.number_format(microtime(true) - $time, 2).'s');
            $pb->advance();
        }
        $pb->finish();
        return $posts;
    }


    protected function renderPage(Post $post)
    {
        Liquid::set('INCLUDE_ALLOW_EXT', true);
        Liquid::set('INCLUDE_PREFIX', './_includes/');
        Liquid::set('HAS_PROPERTY_METHOD', 'get');
        $frontMatter = $post->getFrontMatter();
        $content = $post->getContent();

        //If we have a layout
        if (isset($frontMatter['layout'])) {
            $config = [
                'content' => $content,
                'page' => $post,
                'post' => $post,
                'site' => $this->config,
            ];

            try {
                return $this->renderWithLayout($frontMatter['layout'], $config);
            } catch (FileNotFoundException $err) {
                throw new FileNotFoundException($err->getMessage(). ' for post "'.$post->getTitle().'"');
            }
        }

        return $content;
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
            $template = new Template('./_includes/', new File(['cache_dir' => '/tmp/']));
            $template->registerTag('highlight', Highlight::class);
            $template->registerTag('post_url', PostUrl::class);
            $template->parse($layoutContent);
            $config['content'] = $template->render($config);
            return $this->renderWithLayout($layoutFrontMatter['layout'], $config);
        }

        $template = new Template('./_includes/', new File(['cache_dir' => '/tmp/']));
        $template->registerTag('highlight', Highlight::class);
        $template->registerTag('post_url', PostUrl::class);
        $template->parse($layoutContent);
        return $template->render($config);
    }

}