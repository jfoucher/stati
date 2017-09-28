<?php
/**
 * StaticFileWriter.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 00:02
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
