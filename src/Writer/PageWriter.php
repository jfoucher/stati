<?php
/**
 * StaticFileWriter.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 00:02
 */

namespace Stati\Writer;

use Stati\Entity\Collection;
use Symfony\Component\Filesystem\Filesystem;
use Stati\Entity\Doc;

class PageWriter extends Writer
{
    public function writeAll()
    {
        $fs = new Filesystem();
        $destDir = $this->site->getDestination();

        foreach ($this->site->getPages() as $doc) {
            /**
             * @var Doc $doc
             */
            $dest = str_replace('//', '/', $destDir.'/'.$doc->getPath());
            $fs->dumpFile($dest, $doc->getOutput());
        }
    }
}