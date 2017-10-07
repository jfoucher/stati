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

use Stati\Entity\Page;
use Stati\Parser\ContentParser;
use Liquid\Liquid;

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

        $this->content = shell_exec('echo \''.$contentPart.'\''.' | scss --load-path='.$sassDir.' --style='.$sassStyle.' --compass');
        file_put_contents($cacheDir . '/' . $this->cacheFileName, $this->content);
        return $this->content;
    }

    public function getPath()
    {
        $extension = $this->file->getExtension();
        if ($extension === 'scss' || $extension === 'sass') {
            $extension = 'css';
        }
        return str_replace('//', '/', '/'.$this->file->getRelativePath().'/'.pathinfo($this->file->getBasename(), PATHINFO_FILENAME).'.'.$extension);
    }
}
