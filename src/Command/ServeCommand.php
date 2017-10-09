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

use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;
use Stati\Exception\FileNotFoundException;
use Stati\Renderer\PageRenderer;
use Stati\Renderer\PostRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ServeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setAliases(['s'])
            ->setDescription('Serve static site from localhost')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port the server should run on', '4000')
            ->addOption('no-cache', 'nc', InputOption::VALUE_NONE)
            ->addOption('watch', 'w', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getOption('port');

        $command = $this->getApplication()->find('generate');

        $command->run(new ArrayInput([
            '--no-cache' => $input->getOption('no-cache'),
            '--watch' => $input->getOption('watch'),
            '--serve' => true,
            '--port' => $port
        ]), new ConsoleOutput($output->getVerbosity()));
    }
}
