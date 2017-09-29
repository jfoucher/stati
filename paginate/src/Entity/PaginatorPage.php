<?php
/**
 * Post.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 23:05
 */

namespace Stati\Plugin\Paginate\Entity;

use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Liquid\Template;
use Liquid\Liquid;
use Liquid\Cache\File;
use Stati\Liquid\Block\Highlight;
use Stati\Entity\Doc;

class PaginatorPage extends Doc
{
    protected $currentPage;
    protected $currentPagePath;

    public function __construct(SplFileInfo $file, $site = null, $page = 1, $currentPagePath = '')
    {
        parent::__construct($file, $site);
        $this->currentPage = $page;
        $this->currentPagePath = $currentPagePath;
    }

    public function getPath()
    {
        // This overwrites the original index page
        // TODO find a better way to do this
        $path = $this->currentPagePath;

        //First page and  root index file
        if ($this->currentPage === 1 && $this->file->getRelativePathname() === 'index.html') {
            return '/index.html';
        }
        $extension = $this->file->getExtension();
        if ($extension !== 'html' || !$this->currentPagePath) {
            return '';
        }

        if (substr($path, -5) !== '.html') {
            $path = str_replace('//', '/', $path.'/index.html');
        }
        return $path;
    }

    /**
     * @return string
     */
    public function getContent()
    {
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
        if (is_file($cacheDir.md5($content.$this->currentPage))) {
            return file_get_contents($cacheDir.md5($content.$this->currentPage));
        }
        $template = new Template('./_includes/');
        $template->registerTag('highlight', Highlight::class);
        $template->parse($contentPart);
        $config = [
            'page' => $this,
            'post' => $this,
            'site' => $this->site,
            'paginator' => $this->site->paginator
        ];


        $this->content = $template->render($config);
        file_put_contents($cacheDir.md5($content.$this->currentPage), $this->content);
        return $this->content;
    }
}
