<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Writer;

use Stati\Entity\StaticFile;
use Stati\Writer\Writer;
use Symfony\Component\Filesystem\Filesystem;

class StaticFileWriter extends Writer
{
    public function writeAll()
    {
        $fs = new Filesystem();
        $destDir = $this->site->getDestination();
        foreach ($this->site->getStaticFiles() as $file) {
            /**
             * @var StaticFile $file
             */
            $dest = str_replace('//', '/', $destDir.'/'.$file->getRelativePathname());
            $fs->dumpFile($dest, $file->getContents());
        }
    }
}
