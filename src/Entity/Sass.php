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
use Stati\Site\Site;
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
        $this->cacheFileName = $this->getSlug() . '-' . sha1($content . implode('', Site::arrayFlattenToString($this->getFrontMatter())) . $this->site->__toString());
        if (is_file($cacheDir . '/' . $this->cacheFileName)) {
            return file_get_contents($cacheDir . '/' . $this->cacheFileName);
        }

        $sassDir = $this->getSassDir();

        $sassStyle = ($this->site->sass && isset($this->site->sass['style'])) ? $this->site->sass['style'] : 'nested';

        if (strpos('compressed', $sassStyle) !== false) {
            $sassStyle = 'compressed';
        }

        $parser = new ContentParser();
        $contentPart = $parser::parse($content);

        $liquidParsed = $this->getLiquidParsed($contentPart);

        file_put_contents($cacheDir . '/' . $this->cacheFileName . '_liquid', $liquidParsed);

        $this->content = shell_exec('scss --load-path='.$sassDir.' --style='.$sassStyle.' --compass <' . escapeshellarg($cacheDir . '/' . $this->cacheFileName . '_liquid'));

        file_put_contents($cacheDir . '/' . $this->cacheFileName, $this->content);

        return $this->content;
    }

    private function getLiquidParsed($content)
    {
        //Parse with Liquid

        $template = new Template(Liquid::get('INCLUDE_PREFIX'));

        $this->site->getDispatcher()->dispatch(TemplateEvents::WILL_PARSE_TEMPLATE, new WillParseTemplateEvent($this->site, $template));
        try {
            $template->parse($content);

            $vars = [
                'site' => $this->site
            ];

            $this->site->getDispatcher()->dispatch(TemplateEvents::SETTING_TEMPLATE_VARS, new SettingTemplateVarsEvent($this->site, $vars, $this));
            return $template->render($vars);
        } catch (LiquidException $err) {
            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent(
                'error',
                ['Could not render the file '.$this->getFile()->getRelativePathname().' '.$err->getMessage()]
            ));
            return '';
        }
    }

    public function getOutputPath()
    {
        $extension = $this->file->getExtension();
        if ($extension === 'scss' || $extension === 'sass') {
            $extension = 'css';
        }
        return str_replace('//', '/', '/'.$this->file->getRelativePath().'/'.pathinfo($this->file->getBasename(), PATHINFO_FILENAME).'.'.$extension);
    }

    private function getSassDir()
    {
        $sassDir = isset($this->site->getConfig()['sass']['sass_dir']) ? $this->site->getConfig()['sass']['sass_dir'] : '_sass';

        $sassDir = str_replace('//', '/', $this->site->getConfig()['source'] . '/' . $sassDir);

        return $sassDir;
    }
}
