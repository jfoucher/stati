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
use Stati\Event\ConsoleOutputEvent;
use Stati\Site\SiteEvents;
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
            if (!$collection->getConfigItem('output')) {
                continue;
            }
            foreach ($collection->getDocs() as $doc) {
                /**
                 * @var Doc $doc
                 */
                if ($doc->getOutputPath()) {
                    $dest = str_replace('//', '/', $destDir.'/'.$doc->getOutputPath());
                    $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent(
                        'text',
                        ['Writing the file ' . $doc->getFile()->getRelativePathname() . ' to ' . $dest]
                    ));
                    $fs->dumpFile($dest, $doc->getOutput());
                }
            }
        }
    }
}
