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
                if ($doc->getOutputPath()) {
                    $dest = str_replace('//', '/', $destDir.'/'.$doc->getOutputPath());
                    $fs->dumpFile($dest, $doc->getOutput());
                }
            }
        }
    }
}
