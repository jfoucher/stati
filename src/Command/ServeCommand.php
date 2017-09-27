<?php

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


class ServeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setAliases(['s'])
            ->setDescription('Serve static site from localhost')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = new Application('Stati', '@package_version@');
        $application->add(new GenerateCommand());

        $command = $application->find('generate');
        $returnCode = $command->run(new ArrayInput([]), new ConsoleOutput($output->getVerbosity()));
        $style = new SymfonyStyle($input, $output);
        if ($returnCode === 0) {
            $style->success([
                'Stati is now serving your website',
                'Open http://localhost:4000 to view it',
                'Press Ctrl-C to quit.'
            ]);
            shell_exec('cd _site && php -S localhost:4000 &');

        }
    }

}