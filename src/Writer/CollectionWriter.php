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

class CollectionWriter extends Writer
{
    public function writeAll()
    {
        $fs = new Filesystem();
        $destDir = $this->site->getDestination();
        foreach ($this->site->getCollections() as $collection) {
            /**
             * @var Collection $collection
             */
            foreach ($collection->getDocs() as $doc) {
                /**
                 * @var Doc $doc
                 */
                if ($doc->getPath()) {
                    $dest = str_replace('//', '/', $destDir.'/'.$doc->getPath());
                    $fs->dumpFile($dest, $doc->getOutput());
                }
            }
        }
    }
}
