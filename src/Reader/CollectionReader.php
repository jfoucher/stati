<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Reader;

use Symfony\Component\Finder\Finder;
use Stati\Entity\Post;
use Stati\Entity\Collection;

/**
 * Renders collection defined in _config.yml
 * Class CollectionRenderer
 * @package Stati\Renderer
 */
class CollectionReader extends Reader
{
    public function read()
    {
        $config = $this->site->getConfig();
        if (!isset($config['collections']) || !is_array($config['collections'])) {
            return null;
        }

        $collections = [];
        foreach ($config['collections'] as $collectionName => $collectionData) {
            $collection = new Collection($collectionName, $collectionData);
            $finder = new Finder();
            $finder
                ->in('./')
                ->path(sprintf('/^_%s/', $collectionName))
                ->files()
                ->name('/(.md|.mkd|.markdown)$/')
            ;

            foreach ($config['exclude'] as $exclude) {
                $finder->notName($exclude);
                $finder->notPath($exclude);
            }

            $posts = [];

            foreach ($finder as $file) {
                $post = new Post($file, $collectionData);
                $post->setSite($this->site);
                if ($post->getDate()->getTimestamp() <= date_create()->getTimestamp() && $post->published !== false) {
                    $posts[] = $post;
                }
            }

            usort($posts, function ($a, $b) {
                if (!$a->getDate() || !$b->getDate()) {
                    return 0;
                }
                return $a->getDate()->getTimestamp() > $b->getDate()->getTimestamp() ? -1 : 1;
            });


            $collection->setDocs($posts);
            $collections[$collectionName] = $collection;
        }

        return $collections;
    }
}
