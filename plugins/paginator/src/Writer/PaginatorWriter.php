<?php
/**
 * PaginatorWriter.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 21:35
 */

namespace Stati\Plugin\Paginator\Writer;

use Stati\Entity\Doc;
use Stati\Writer\Writer;
use Symfony\Component\Filesystem\Filesystem;

class PaginatorWriter extends Writer
{
    public function writeAll()
    {
        $fs = new Filesystem();
        $destDir = $this->site->getDestination();
        $paginator = $this->site->paginator;
        while ($paginator->next_page) {
            foreach ($paginator->getPosts() as $doc) {
                /**
                 * @var Doc $doc
                 */
                $dest = str_replace('//', '/', $destDir.'/'.$doc->getPath());
                $fs->dumpFile($dest, $doc->getOutput());
            }
            $paginator->setPage($paginator->getPage() + 1);
        }
    }
}
