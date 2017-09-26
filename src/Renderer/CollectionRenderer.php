<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Renderer;

use Stati\Entity\Collection;
use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class CollectionRenderer extends Renderer
{
    public function renderAll()
    {
        $collections = [];
        foreach ($this->site->getCollections() as $collection) {
            /**
             * @var Collection $collection
             */
            $docs = [];
            foreach ($collection->getDocs() as $doc) {
                /**
                 * @var Page $doc
                 */
                $doc->setSite($this->site);
                var_dump($doc->getPath());
                $docs[] = $this->render($doc);
            }
            $collection->setDocs($docs);
            $collections[] = $collection;
        }
        return $collections;
    }
}