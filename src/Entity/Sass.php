<?php
/**
 * Sass.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 23:55
 */

namespace Stati\Entity;

use Stati\Entity\Page;
use Stati\Parser\ContentParser;

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

        $cacheDir = '/tmp/sass_cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        $sassDir = ($this->site->sass && isset($this->site->sass['sass_dir'])) ? $this->site->sass['sass_dir'] : './_sass/';
        $sassStyle = ($this->site->sass && isset($this->site->sass['style'])) ? $this->site->sass['style'] : 'nested';

        $parser = new ContentParser();
        $content = $this->file->getContents();
        $contentPart = $parser::parse($content);
        if (is_file($cacheDir.md5($content.$sassDir.$sassStyle))) {
            return file_get_contents($cacheDir.md5($content.$sassDir.$sassStyle));
        }
        $this->content = shell_exec('echo \''.$contentPart.'\''.' | scss --load-path='.$sassDir.' --style='.$sassStyle.' --compass');
        file_put_contents($cacheDir.md5($content.$sassDir.$sassStyle), $this->content);
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
