<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Renderer;

use Liquid\LiquidException;
use Stati\Entity\Doc;
use Stati\Event\ConsoleOutputEvent;
use Stati\Event\WillParseTemplateEvent;
use Stati\Site\Site;
use Liquid\Liquid;
use Stati\Exception\FileNotFoundException;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;
use Stati\Liquid\Template\Template;
use Stati\Liquid\TemplateEvents;
use Stati\Event\SettingTemplateVarsEvent;
use Stati\Site\SiteEvents;
use Liquid\Cache\File;

class Renderer
{
    /**
     * @var Site
     */
    protected $site;

    /**
     * @var array Memo for layouts
     */
    protected $layouts;

    protected $cacheFileName;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function render(Doc $doc)
    {
        $frontMatter = $doc->getFrontMatter();
        $content = $doc->getContent();

        // If file content is the same, frontMatter is the same and site config is the same, read cache file
        $cacheDir = $this->site->getConfig()['cache_dir'] . '/post_cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        if ($this->cacheFileName && is_file($cacheDir . '/' . $this->cacheFileName)) {
            $content = file_get_contents($cacheDir . '/' . $this->cacheFileName);
            $doc->setOutput($content);
            return $doc;
        }

        //If we have a layout
        if (isset($frontMatter['layout'])) {
            $vars = [
                'content' => $content,
                'page' => $doc,
                'post' => $doc,
                'site' => $this->site,
            ];

            $this->site->getDispatcher()->dispatch(TemplateEvents::SETTING_LAYOUT_TEMPLATE_VARS, new SettingTemplateVarsEvent($this->site, $vars, $doc));
            try {
                if (in_array($doc->getFile()->getExtension(), explode(',', $this->site->getConfig()['markdown_ext']))) {
                    $ext = 'html';
                } else {
                    $ext = $doc->getFile()->getExtension();
                }
                $content = $this->renderWithLayout($frontMatter['layout'], $vars, $ext);
            } catch (FileNotFoundException $err) {
                throw new FileNotFoundException($err->getMessage(). ' for post "'.$doc->getTitle().'"');
            }
        }
        if ($this->cacheFileName) {
            file_put_contents($cacheDir . '/' . $this->cacheFileName, $content);
        }

        $doc->setOutput($content);
        return $doc;
    }

    protected function renderWithLayout($layoutFile, $config, $extension = 'html')
    {
        Liquid::set('INCLUDE_ALLOW_EXT', true);
        Liquid::set('INCLUDE_PREFIX', $this->site->getConfig()['includes_dir']);
        Liquid::set('HAS_PROPERTY_METHOD', 'get');

        $folder = str_replace('//', '/', $this->site->getConfig()['layouts_dir'] . '/');

        if (isset($this->layouts[$layoutFile])) {
            $layoutFrontMatter = $this->layouts[$layoutFile]['frontMatter'];
            $layoutContent = $this->layouts[$layoutFile]['content'];
        } else {
            $layout = @file_get_contents($folder.$layoutFile.'.'.$extension);
            if (!$layout) {
                throw new FileNotFoundException('Layout file "'.$layoutFile.'" not found in layout folder '.$folder);
            }

            $layoutFrontMatter = FrontMatterParser::parse($layout);
            $layoutContent = ContentParser::parse($layout);
            $this->layouts[$layoutFile] = [
                'frontMatter' => $layoutFrontMatter,
                'content' => $layoutContent,
            ];
        }

        if (isset($layoutFrontMatter['layout'])) {
            $template = new Template(Liquid::get('INCLUDE_PREFIX')/*, new File(['cache_dir' => sys_get_temp_dir()])*/);
            $this->site->getDispatcher()->dispatch(TemplateEvents::WILL_PARSE_TEMPLATE, new WillParseTemplateEvent($this->site, $template));

            $template->parse($layoutContent);
            $config['content'] = $template->render($config);

            return $this->renderWithLayout($layoutFrontMatter['layout'], $config);
        }

        $template = new Template(Liquid::get('INCLUDE_PREFIX')/*, new File(['cache_dir' => sys_get_temp_dir()])*/);
        $this->site->getDispatcher()->dispatch(TemplateEvents::WILL_PARSE_TEMPLATE, new WillParseTemplateEvent($this->site, $template));
        $template->parse($layoutContent);

        return $template->render($config);
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }
}
