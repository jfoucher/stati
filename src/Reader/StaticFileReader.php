<?php
/**
 * StaticFileReader.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 22:09
 */

namespace Stati\Reader;

use Symfony\Component\Finder\Finder;
use Stati\Entity\StaticFile;

class StaticFileReader extends Reader
{
    public function read()
    {
        $finder = new Finder();
        $finder
            ->in('./')
            ->files()
            ->notPath('/^_/')
            ->notPath('node_modules')
            ->notContains('/---\s+(.*)\s+---\s+/s')
            ->notName('/^_/');

        $staticFiles = [];

        foreach ($finder as $file) {
            $staticFiles[] = new StaticFile($file->getPathname(), $file->getRelativePath(), $file->getRelativePathname());
        }

        return $staticFiles;
    }
}
