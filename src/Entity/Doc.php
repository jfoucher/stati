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
use Stati\Liquid\Tag\Link;
use Stati\Liquid\TemplateEvents;
use Stati\Site\Site;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Parser\MarkdownParser;
use Stati\Liquid\Template\Template;
use Liquid\Liquid;
use Stati\Liquid\Block\Highlight;
use Stati\Liquid\Tag\PostUrl;
use Liquid\Cache\File;
use Stati\Liquid\Filter\SiteFilter;
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


    /**
     * Doc constructor.
     * @param SplFileInfo $file
     * @param null $site
     */

    public function __construct(SplFileInfo $file, $site = null)
    {
        $this->file = $file;
        $this->site = $site;
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
        $markdownParser = new MarkdownParser();
        $content = $this->file->getContents();
        $contentPart = $parser::parse($content);

        $include = null;

        $template = new Template(Liquid::get('INCLUDE_PREFIX')/*, new File(['cache_dir' => '/tmp/'])*/);

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
        if (in_array($this->file->getExtension(), $markdownExtensions)) {
            $this->content = $markdownParser->text($liquidParsed);
        } else {
            $this->content = $liquidParsed;
        }

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
        if (isset($this->getFrontMatter()['title'])) {
            $this->title = $this->getFrontMatter()['title'];
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

        $path = $this->getPath();
        if (substr($path, -10) === 'index.html') {
            $path = substr($path, 0, -10);
        }
        $this->url = str_replace('//', '/', $path);
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPath()
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

        $this->setPath($link);

        return $this->path;
    }

    /**
     * @param string $path
     * @return Doc
     */
    public function setPath($path)
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
        $defaults = [];
        // TODO GET defaults from site config if available, and merge with file frontmatter
        $config = $this->getSite()->getConfig();
        if (isset($config['defaults'])) {
            foreach ($config['defaults'] as $def) {
                if (str_replace('/', '', $def['scope']['path']) === str_replace(['/', '.'], '', $this->file->getRelativePath())) {
                    $defaults = $def['values'];
                }
            }
        }

        $fileFrontMatter = $parser::parse($this->file->getContents());

        $this->setFrontMatter(array_merge($defaults, $fileFrontMatter));
        return $this->frontMatter;
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
        if ($this->slug) {
            return $this->slug;
        }
        $filename = pathinfo($this->file->getBasename(), PATHINFO_FILENAME);
        try {
            $date = new \DateTime(substr($filename, 0, 10));
            $this->slug = substr($filename, 11);
        } catch (\Exception $err) {
//            echo $err->getMessage();
            $this->slug = $filename;
        }
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        if (isset($this->getFrontMatter()['permalink'])) {
            return $this->getFrontMatter()['permalink'];
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

        if (method_exists($this, 'get'.ucfirst($item))) {
            return $this->{'get'.ucfirst($item)}();
        }
        if (isset($this->getFrontMatter()[$item])) {
            return $this->getFrontMatter()[$item];
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
                        $replace = implode('/', $this->getFrontMatter()['categories']);
                        break;
                    case ':slug':
                        $replace = $this->getSlug();
                        break;
                    default:
                }

                $link = str_replace($token, $replace, $link);
            }
        }
        return $link;
    }
}
