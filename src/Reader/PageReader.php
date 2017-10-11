<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Reader;

use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Stati\Reader\Reader;
use Stati\Site\SiteEvents;
use Stati\Event\ConsoleOutputEvent;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class PageReader extends Reader
{
    public function read()
    {
        $config = $this->site->getConfig();
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in('./')
            ->notPath('/^_/')
            ->files()
            ->notName('/^_/')
            ->contains('/\A---\s*\r?\n/')
        ;

        foreach ($config['exclude'] as $exclude) {
            if (strpos($exclude, '/') === 0) {
                $exclude = substr($exclude, 1);
            }
            if (strpos($exclude, '*') !== false) {
                //If pattern is a glob, treat as such
                $finder->notName($exclude);
                $finder->notPath($exclude);
            } else {
                // Otherwise, match start and end of string
                $finder->notName('|^'.$exclude.'|');
                $finder->notPath('|^'.$exclude.'|');
            }
        }

        $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('section', ['Reading pages', OutputInterface::VERBOSITY_VERBOSE]));

        $files = [];
        foreach ($finder as $file) {
            $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Reading file '.$file->getRelativePathname()], OutputInterface::VERBOSITY_DEBUG));
            if ($file->getExtension() === "scss" || $file->getExtension() === "sass") {
                $page = new Sass($file, $this->site);
            } else {
                $page = new Page($file, $this->site);
            }
            $files[] = $page;
        }

        $this->site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Read ' . count($files) . ' pages'], OutputInterface::VERBOSITY_DEBUG));

        return $files;
    }
}
