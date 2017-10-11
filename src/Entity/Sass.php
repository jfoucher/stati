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
use Stati\Entity\Page;
use Stati\Event\ConsoleOutputEvent;
use Stati\Event\SettingTemplateVarsEvent;
use Stati\Event\SiteEvent;
use Stati\Event\WillParseTemplateEvent;
use Stati\Liquid\Template\Template;
use Stati\Liquid\TemplateEvents;
use Stati\Parser\ContentParser;
use Liquid\Liquid;
use Stati\Site\SiteEvents;

class Sass extends Page
{
    /**
     * @return string
     */
    public function getContent()
    {
        if ($this->content !== null) {
            return $this->content;
        }




        $content = $this->file->getContents();
        $cacheDir = $this->site->getConfig()['cache_dir'] . '/sass_cache';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $this->cacheFileName = $this->getSlug() . '-' . sha1($content . implode('', Liquid::arrayFlatten($this->getFrontMatter())));
        if (is_file($cacheDir . '/' . $this->cacheFileName)) {
            return file_get_contents($cacheDir . '/' . $this->cacheFileName);
        }

        $sassDir = ($this->site->sass && isset($this->site->sass['sass_dir'])) ? $this->site->sass['sass_dir'] : './_sass/';
        $sassStyle = ($this->site->sass && isset($this->site->sass['style'])) ? $this->site->sass['style'] : 'nested';

        $parser = new ContentParser();
        $contentPart = $parser::parse($content);

        //Parse with Liquid

        $template = new Template(Liquid::get('INCLUDE_PREFIX'));

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

        $this->content = shell_exec('echo \''.$liquidParsed.'\''.' | scss --load-path='.$sassDir.' --style='.$sassStyle.' --compass');
        file_put_contents($cacheDir . '/' . $this->cacheFileName, $this->content);
        return $this->content;
    }

    public function getOutputPath()
    {
        $extension = $this->file->getExtension();
        if ($extension === 'scss' || $extension === 'sass') {
            $extension = 'css';
        }
        return str_replace('//', '/', '/'.$this->file->getRelativePath().'/'.pathinfo($this->file->getBasename(), PATHINFO_FILENAME).'.'.$extension);
    }
}
