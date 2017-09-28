<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Reader;

use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Stati\Reader\Reader;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class PageReader extends Reader
{
    public function read()
    {
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in('./')
            ->notPath('/^_/')
            ->files()
            ->notName('/^_/')
            ->notPath('node_modules')
            ->contains('/---\s+(.*)\s+---\s+/s');

        $files = [];
        foreach ($finder as $file) {
            if ($file->getExtension() === "scss" || $file->getExtension() === "sass") {
                $page = new Sass($file, $this->config);
            } else {
                $page = new Page($file, $this->config);
            }
            $files[] = $page;
        }

        return $files;
    }
}
