<?php
/**
 * PostRenderer.php
 *
 * Created By: jonathan
 * Date: 24/09/2017
 * Time: 22:17
 */

namespace Stati\Reader;

use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Stati\Reader\Reader;
use Symfony\Component\Yaml\Yaml;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class DataReader extends Reader
{
    public function read()
    {
        $config = $this->site->getConfig();
        $dataDir = $config['data_dir'];
        if (!is_dir($dataDir)) {
            return [];
        }
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in('./_data/')
            ->files()
            ->notName('/^_/')
            ->name('/(.yml|.yaml)$/')
        ;

        foreach ($config['exclude'] as $exclude) {
            $finder->notName($exclude);
            $finder->notPath($exclude);
        }

        $data = [];
        foreach ($finder as $file) {
            $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $data[$name] = Yaml::parse($file->getContents());
        }

        return $data;
    }
}
