<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Reader;

use Stati\Event\ConsoleOutputEvent;
use Stati\Site\SiteEvents;
use Symfony\Component\Console\Output\OutputInterface;
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
            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('section', ['Reading collection '.$collectionName]));

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
                $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Reading file '.$file->getRelativePathname()], OutputInterface::VERBOSITY_DEBUG));
                $post = new Post($file, $this->site);
                if ($post->getDate() && $post->getDate()->getTimestamp() >= date_create()->getTimestamp() && $post->published === false) {
                    continue;
                }
                $post->setCollection($collection);
                $posts[] = $post;
            }

            usort($posts, function ($a, $b) {
                if (!$a->getDate() || !$b->getDate()) {
                    return 0;
                }
                return $a->getDate()->getTimestamp() > $b->getDate()->getTimestamp() ? -1 : 1;
            });

            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', [$collection->getLabel().' has '.count($posts).' documents.'], OutputInterface::VERBOSITY_VERY_VERBOSE));

            $collection->setDocs($posts);
            $collections[$collectionName] = $collection;
        }

        return $collections;
    }
}
