<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
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
            if (strpos($exclude, '*') !== false) {
                //If pattern is a glob, treat as such
                $finder->notName($exclude);
                $finder->notPath($exclude);
            } else {
                // Otherwise, match start and end of string
                $finder->notName('/^'.$exclude.'$/');
                $finder->notPath('/^'.$exclude.'$/');
            }
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
