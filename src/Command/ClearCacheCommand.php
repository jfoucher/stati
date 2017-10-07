<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Command;

use Stati\Event\ConsoleOutputEvent;
use Stati\Site\Site;
use Stati\Site\SiteEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Phar;
use Symfony\Component\Filesystem\Filesystem;

class ClearCacheCommand extends Command
{
    /**
     * @var Site
     */
    protected $site;

    /**
     * @var SymfonyStyle
     */
    protected $style;

    protected function configure()
    {
        $this
            ->setName('clear-cache')
            ->setAliases(['cc'])
            ->setDescription('Clear the generated cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->style = new SymfonyStyle($input, $output);

        // Read config file
        $configFile = './_config.yml';

        if (is_file($configFile)) {
            $config = Yaml::parse(file_get_contents($configFile));
        } else {
            $this->style->error('No config file present. Are you in a jekyll directory ?');
            return 1;
        }

        $this->site = new Site($config);

        // delete cache
        $fs = new Filesystem();
        $fs->remove($this->site->getConfig()['cache_dir']);

        $this->style->title('Cache cleared');
        return 0;
    }
}
