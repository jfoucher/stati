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
use Stati\Entity\Post;

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
            $collectionDocs = $collection->getDocs();

            foreach ($collectionDocs as $k => $doc) {
                /**
                 * @var Post $doc
                 */
                $doc->setSite($this->site);

                if ($collection->getLabel() === 'posts') {
                    if ($k > 0) {
                        $doc->setNext($collectionDocs[$k - 1]);
                    }
                    if ($k < count($collectionDocs) -1) {
                        $doc->setPrevious($collectionDocs[$k + 1]);
                    }
                }
                $docs[] = $this->render($doc);
            }

            $collection->setDocs($docs);

            $collections[$collection->getLabel()] = $collection;
        }
        return $collections;
    }
}
