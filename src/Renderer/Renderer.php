<?php

/*
 * This file is part of the Stati package.
 *
 * Copyright 2017 Jonathan Foucher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package Stati
 */

namespace Stati\Renderer;

use Liquid\Exception\NotFoundException;
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

    protected $layoutsDir;

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
        $this->layoutsDir = str_replace('//', '/', $this->site->getConfig()['source'] . '/' . $this->site->getConfig()['layouts_dir'] . '/');
        //If we have a layout
        if ($doc->getLayout() && $doc->getLayout() !== 'none' && $doc->getLayout() !== 'nil') {
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
                $content = $this->renderWithLayout($doc->getLayout(), $vars, $ext);
            } catch (NotFoundException $err) {
                $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('error', [['Could not render page'.$doc->getTitle() . ' because of the following error', $err->getMessage(). ' for post "'.$doc->getTitle().'"']]));
                $doc->setOutput($content);
                return $doc;
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
        Liquid::set('INCLUDE_PREFIX', $this->site->getConfig()['source'] . '/' . $this->site->getConfig()['includes_dir']);
        Liquid::set('HAS_PROPERTY_METHOD', 'get');

        if (isset($this->layouts[$layoutFile])) {
            $layoutFrontMatter = $this->layouts[$layoutFile]['frontMatter'];
            $layoutContent = $this->layouts[$layoutFile]['content'];
        } else {
            $layout = @file_get_contents($this->layoutsDir.$layoutFile.'.'.$extension);
            if (!$layout) {
                throw new NotFoundException('Layout file "'.$layoutFile.'.'.$extension.'" not found in layout folder '.$this->layoutsDir);
            }

            $layoutFrontMatter = FrontMatterParser::parse($layout);
            $layoutContent = ContentParser::parse($layout);
            $this->layouts[$layoutFile] = [
                'frontMatter' => $layoutFrontMatter,
                'content' => $layoutContent,
            ];
        }

        if (isset($layoutFrontMatter['layout']) && $layoutFrontMatter['layout'] !== 'none' && $layoutFrontMatter['layout'] !== 'nil') {
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
