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

class ServeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setAliases(['s'])
            ->setDescription('Serve static site from localhost')
            ->addArgument('port', InputArgument::OPTIONAL, 'Port the server should run on', '4000')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getArgument('port');
        $command = $this->getApplication()->find('generate');

        $returnCode = $command->run(new ArrayInput([]), new ConsoleOutput($output->getVerbosity()));
        $style = new SymfonyStyle($input, $output);
        if ($returnCode === 0) {
            $style->success([
                'Stati is now serving your website',
                'Open http://localhost:'.$port.' to view it',
                'Press Ctrl-C to quit.'
            ]);
            shell_exec('cd _site && php -S localhost:'.$port.' &');
        }
    }
}
