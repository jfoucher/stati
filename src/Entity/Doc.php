<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Entity;

use Liquid\LiquidException;
use Stati\Event\SettingTemplateVarsEvent;
use Stati\Event\WillParseTemplateEvent;
use Stati\Liquid\TemplateEvents;
use Stati\Site\Site;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Parser\MarkdownParser;
use Stati\Liquid\Template\Template;
use Liquid\Liquid;
use Stati\Site\SiteEvents;
use Stati\Event\ConsoleOutputEvent;

class Doc
{
    /**
     * HTML content of this post without layout
     * @var string
     */
    protected $content;

    /**
     * The post title
     * @var string
     */
    protected $title;

    /**
     * HTML content of this post with layout
     * @var string
     */
    protected $output;

    /**
     * Original file for this post
     * @var SplFileInfo
     */
    protected $file;

    /**
     * URL for this post
     * @var string
     */
    protected $url;

    /**
     * Slug for this post
     * @var string
     */
    protected $slug;

    /**
     * Path for this post
     * @var string
     */
    protected $path;

    /**
     * Excerpt for this document
     * @var string
     */
    protected $excerpt;

    /**
     * Date for this post
     * @var \DateTime
     */
    protected $date;

    /**
     * front matter for this post
     * @var array
     */
    protected $frontMatter;

    /**
     * Permalink configuration
     * @var string
     */
    protected $permalink;

    /**
     * Site configuration configuration
     * @var Site
     */
    protected $site;


    protected $cacheFileName;

    const DATELESS_FILENAME_MATCHER = '!^(?:.+/)*(.*)(\.[^.]+)$!';
    const DATE_FILENAME_MATCHER = '!^(?:.+/)*(\d{2,4}-\d{1,2}-\d{1,2})-(.*)(\.[^.]+)$!';

    /**
     * Doc constructor.
     * @param SplFileInfo $file
     * @param null $site
     */

    public function __construct(SplFileInfo $file, $site = null)
    {
        $this->file = $file;
        $this->site = $site;
        if (preg_match(self::DATE_FILENAME_MATCHER, $this->file->getBasename(), $matches)) {
            $this->date = new \DateTime($matches[1]);
            $this->slug = $matches[2];
        } elseif (preg_match(self::DATELESS_FILENAME_MATCHER, $this->file->getBasename(), $matches)) {
            $this->slug = $matches[1];
        }
        $this->getFrontMatter();
    }

    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        Liquid::set('INCLUDE_ALLOW_EXT', true);
        Liquid::set('INCLUDE_PREFIX', $this->site->getConfig()['includes_dir']);
        Liquid::set('HAS_PROPERTY_METHOD', 'get');

        $parser = new ContentParser();

        $content = $this->file->getContents();
        $contentPart = $parser::parse($content);
        $cacheDir = $this->site->getConfig()['cache_dir'] . '/post_cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $this->cacheFileName = $this->getSlug() . '-' . sha1($content . implode('', Liquid::arrayFlatten($this->frontMatter)));
        if (is_file($cacheDir . '/' . $this->cacheFileName)) {
            return file_get_contents($cacheDir . '/' . $this->cacheFileName);
        }

        $template = new Template(Liquid::get('INCLUDE_PREFIX')/*, new File(['cache_dir' => sys_get_temp_dir().'/'])*/);

        $this->site->getDispatcher()->dispatch(TemplateEvents::WILL_PARSE_TEMPLATE, new WillParseTemplateEvent($this->site, $template));
        try {
            $template->parse($contentPart);

            $vars = [
                'site' => $this->site
            ];

            $this->site->getDispatcher()->dispatch(TemplateEvents::SETTING_TEMPLATE_VARS, new SettingTemplateVarsEvent($this->site, $vars, $this));
            $liquidParsed = $template->render($vars);
        } catch (LiquidException $err) {
            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent(
                'error',
                ['Could not render the file '.$this->getFile()->getRelativePathname().' '.$err->getMessage()]
            ));
            $liquidParsed = '';
        }

        // Get extensions from site config
        $markdownExtensions = explode(',', $this->site->getConfig()['markdown_ext']);

        $this->content = $liquidParsed;

        if (in_array($this->file->getExtension(), $markdownExtensions)) {
            $markdownParser = new MarkdownParser();
            $this->content = $markdownParser->text($this->content);
        }

        file_put_contents($cacheDir . '/' . $this->cacheFileName, $this->content);

        return $this->content;
    }


    /**
     * @param string $content
     * @return Doc
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->title) {
            return $this->title;
        }
        if (isset($this->frontMatter['title'])) {
            $this->title = $this->frontMatter['title'];
        }
        return $this->title;
    }

    /**
     * @param string $title
     * @return Doc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->url !== null) {
            return $this->url;
        }

        $path = $this->getOutputPath();
        if (substr($path, -10) === 'index.html') {
            $path = substr($path, 0, -10);
        }
        $this->url = str_replace('//', '/', $path);
        return $this->url;
    }

    /**
     * @return string
     */
    public function getOutputPath()
    {
        if ($this->path !== null) {
            return $this->path;
        }
        if (!$this->getContent()) {
            $this->path = null;
            return $this->path;
        }

        $link = $this->getPermalink();
        if ($this->getDate()) {
            $link = $this->getDateLinkPart($link);
        }

        $link = $this->getTextLinkPart($link);

        // if link ends with / we should put an index.html file in that directory
        if (substr($link, -1) === '/') {
            $link .= 'index.html';
        }

        $this->setOutputPath($link);

        return $this->path;
    }

    /**
     * @param string $path
     * @return Doc
     */
    public function setOutputPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return array
     */
    public function getFrontMatter()
    {
        if ($this->frontMatter) {
            return $this->frontMatter;
        }

        $parser = new FrontMatterParser();
        $fileFrontMatter = $parser::parse($this->file->getContents());

        $defaults = $this->getDefaultConfig();
        $this->setFrontMatter(array_merge($defaults, $fileFrontMatter));
        return $this->frontMatter;
    }

    private function getDefaultConfig()
    {
        $defaults = [];
        if ($this->site) {
            $defaults = [];
            // Get defaults from site config if available, and merge with file frontmatter
            $config = $this->site->getConfig();
            if (isset($config['defaults'])) {
                foreach ($config['defaults'] as $def) {
                    if (!isset($def['scope']) || (isset($def['scope']) && isset($def['scope']['path']) && str_replace('/', '', $def['scope']['path']) === str_replace(['/', '.'], '', $this->file->getRelativePath()))) {
                        $defaults = $def['values'];
                    }
                }
            }
        }
        return $defaults;
    }

    /**
     * @param array $frontMatter
     * @return Doc
     */
    public function setFrontMatter($frontMatter)
    {
        $this->frontMatter = $frontMatter;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        if (isset($this->frontMatter['permalink'])) {
            return $this->frontMatter['permalink'];
        }
        return $this->site->permalink;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite($site)
    {
        $this->site = $site;
        $defaults = $this->getDefaultConfig();

        $this->setFrontMatter(array_merge($defaults, $this->frontMatter));
    }


    public function get($item)
    {
        return $this->__get($item);
    }

    public function __get($item)
    {
        if ($item === 'date') {
            if (!$this->getDate()) {
                return null;
            }
            return $this->getDate()->format(DATE_RFC3339);
        }

        if ($item === 'paginate') {
            return $this->site->paginate;
        }

        if ($item === 'path') {
            return $this->file->getRelativePathname();
        }

        if (method_exists($this, 'get'.ucfirst($item))) {
            return $this->{'get'.ucfirst($item)}();
        }
        if (isset($this->frontMatter[$item])) {
            return $this->frontMatter[$item];
        }
        return null;
    }

    public function __toString()
    {
        return $this->getSlug();
    }

    private function getDateLinkPart($link)
    {
        if (preg_match_all('/(:year|:month|:day|:hour)/', $link, $matches, PREG_PATTERN_ORDER)) {
            foreach ($matches[1] as $token) {
                $format = '';
                switch ($token) {
                    case ':year':
                        $format = 'Y';
                        break;
                    case ':month':
                        $format = 'm';
                        break;
                    case ':day':
                        $format = 'd';
                        break;
                    default:
                }
                $link = str_replace($token, $this->getDate()->format($format), $link);
            }
        }
        return $link;
    }

    private function getTextLinkPart($link)
    {
        if (preg_match_all('/(:title|:categories|:slug)/', $link, $matches, PREG_PATTERN_ORDER)) {
            foreach ($matches[1] as $token) {
                $replace = '';
                switch ($token) {
                    case ':title':
                        $replace = $this->getSlug();
                        break;
                    case ':categories':
                        if (isset($this->frontMatter['category'])) {
                            $replace = $this->frontMatter['category'];
                        }
                        if (isset($this->frontMatter['categories'])) {
                            $replace = $this->frontMatter['categories'];
                        }
                        if ($replace && is_array($replace)) {
                            $replace = implode('/', $replace);
                        }

                        break;
                    case ':slug':
                        $replace = $this->getSlug();
                        break;
                    default:
                }

                $link = str_replace($token, strtolower($replace), $link);
            }
        }
        return $link;
    }

    /**
     * @return mixed
     */
    public function getCacheFileName()
    {
        return $this->cacheFileName;
    }
}
