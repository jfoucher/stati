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
        $config = $this->site->getConfig();
        $finder = new Finder();

        $finder
            ->in('./')
            ->files()
            ->notPath('/^_/')
            ->notContains('/\A---\s*\r?\n/')
            ->notName('/^_/');
        foreach ($config['exclude'] as $exclude) {
            $finder->notName($exclude);
            $finder->notPath($exclude);
        }
        $staticFiles = [];

        foreach ($finder as $file) {
//            $type = mime_content_type($file->getRelativePathname());
//            var_dump($type);
//            if (substr($type, 4) === 'text') {
//                if (fgets(fopen($file, 'r')) === '---') {
//                    continue;
//                }
//            }
            $staticFiles[] = new StaticFile($file->getRelativePathname(), $file->getRelativePath(), $file->getRelativePathname());
        }

        return $staticFiles;
    }
}
