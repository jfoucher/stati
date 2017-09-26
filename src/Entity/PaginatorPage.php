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
use Liquid\Template;
use Liquid\Liquid;
use Liquid\Cache\File;
use Stati\Liquid\Block\Highlight;

class PaginatorPage extends Post
{

    public function getPath()
    {
        $extension = $this->file->getExtension();
        if ($extension !== 'html' || !isset($this->siteConfig['paginator'])) {
            return '';
        }
        $path = $this->siteConfig['paginator']->current_page_path;
        if(substr($path, -1) === '/') {
            $path = $path.'index.html';
        }
        return $path;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (!isset($this->siteConfig['paginator'])) {
            return '';
        }
        if ($this->content !== null) {
            return $this->content;
        }

        $cacheDir = '/tmp/post_cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        $parser = new ContentParser();
        $content = $this->file->getContents();
        $contentPart = $parser::parse($content);
        if (is_file($cacheDir.md5($content.$this->siteConfig['paginator']->page))) {
            return file_get_contents($cacheDir.md5($content.$this->siteConfig['paginator']->page));
        }
        $template = new Template('./_includes/');
        $template->registerTag('highlight', Highlight::class);
        $template->parse($contentPart);
        $config = [
            'page' => $this,
            'post' => $this,
            'site' => $this->siteConfig,
        ];
        if (isset($this->siteConfig['paginate']) && isset($this->siteConfig['paginator'])) {
            $config['paginator'] = $this->siteConfig['paginator'];
        }
        $this->content = $template->render($config);
        file_put_contents($cacheDir.md5($content.$this->siteConfig['paginator']->page), $this->content);
        return $this->content;
    }
}