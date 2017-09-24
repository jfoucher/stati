<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Renderer;

use Symfony\Component\Finder\Finder;
use Liquid\Template;
use Liquid\Liquid;
use Stati\Parser\MarkdownParser;
use Stati\Link\Generator;
use Stati\LiquidBlock\Highlight;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\LiquidTag\PostUrl;
use Liquid\Cache\File;

class PostsRenderer
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function render()
    {
        // Get top level files and parse
        $finder = new Finder();
        $finder->depth(' <= 1')
        ->files()
        ->in('./_posts/')
        ->contains('post_url')
        ->name('/(\.mkd|\.md|\.markdown)$/');
        //            ->name('/\.md$/');

        $linkGenerator = new Generator($this->config);
        foreach ($finder as $file) {
            $fileContents = $file->getContents();
            $fileContents = trim($fileContents);

            $rendered = $this->renderPost($fileContents, $this->config, $file);

            //Put rendered file in _site directory, in it's right place
            $path = $linkGenerator->getPathForFile($file);
            $dir = str_replace('//', '/', './_site/'.pathinfo($path,PATHINFO_DIRNAME));
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents('./_site'.$path, $rendered);
        }
        return count($finder);
    }


    private function renderPost($content, $config, SplFileInfo $file = null)
    {
        if($file) {
            var_dump($file->getRelativePathname());
        }

        Liquid::set('INCLUDE_ALLOW_EXT', true);
        Liquid::set('INCLUDE_PREFIX', './_includes/');
        $linkGenerator = new Generator($config);
        $frontMatter = FrontMatterParser::parse($content);
        $content = ContentParser::parse($content);

        //If we have a layout
        if (isset($frontMatter['layout'])) {
            $newConfig = $config;
            //If file is markdown, parse it first before passing it to liquid
            if ($file && ($file->getExtension() === 'md' || $file->getExtension() === 'mkd' || $file->getExtension() === 'markdown')) {
    //                $parsedown = new Parsedown();
    //                $content = $parsedown->text($content);
                $newConfig =  [
                    'url' => $linkGenerator->getUrlForFile($file, 'post'),
                    'site' => $config,
                    'page' => $frontMatter
                ];
            } else {
                $newConfig['page'] = $config['page'];
                $newConfig['site'] = $config['site'];
                $newConfig['url'] = $config['url'];
            }

            //Parse content with liquid also
            $template = new Template('./_includes/');
            $template->registerTag('highlight', Highlight::class);
            $template->registerTag('post_url', PostUrl::class);
            $template->parse($content);
            $rendered = $template->render($newConfig);
            //Use layout as the thing to parse, and pass content as variable.
            $parsedown = new MarkdownParser();
            $newConfig['content'] = $parsedown->text($rendered);
            //Pass frontmatter as page variable
            $content = file_get_contents('./_layouts/'.$frontMatter['layout'].'.html');
            return $this->renderPost($content, $newConfig);
        }

        $template = new Template('./_includes/');
        $template->registerTag('highlight', Highlight::class);
        $template->registerTag('post_url', PostUrl::class);
        $template->parse($content);
        return $template->render($config);
    }

}