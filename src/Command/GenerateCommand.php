<?php

namespace Stati\Command;


use function Composer\Autoload\includeFile;
use Stati\Exception\FileNotFoundException;
use Stati\Paginator\Paginator;
use Stati\Renderer\CollectionReader;
use Stati\Renderer\DirectoryRenderer;
use Stati\Renderer\PageRenderer;
use Stati\Renderer\PostRenderer;
use Stati\Site\Site;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Stati\Entity\StaticFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Stati\Renderer\PaginatorRenderer;
use Symfony\Component\Finder\Finder;
use Phar;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setAliases(['g'])
            ->setDescription('Generate static site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = microtime(true);
        $style = new SymfonyStyle($input, $output);
        // Read config file
        $configFile = './_config.yml';

        if (is_file($configFile)) {
            $config = Yaml::parse(file_get_contents($configFile));
        } else {
            $style->error('No config file present. Are you in a jekyll directory ?');
            return 1;
        }

        $site = new Site($config);
        $this->getPlugins();

        $site->process();

        $elapsed = microtime(true) - $time;
        $style->title('Generated in '.number_format($elapsed, 2).'s');
        return 0;
    }

    private function getPlugins()
    {
        $finder = new Finder();
        if ($dir = Phar::running(false)) {
            $finder->in(dirname($dir).'/plugins/')
                ->name('*.phar')
            ;
            var_dump(dirname($dir).'/plugins/');
        } else {
            $dir = __DIR__;
            $finder->in($dir.'/../../plugins/')
                ->name('autoload.php')
            ;
            var_dump($dir.'/../../plugins/');
        }




        $plugins = [];
        foreach ($finder as $file) {

        }

        return $plugins;
    }

}