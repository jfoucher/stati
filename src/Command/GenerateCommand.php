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
    /**
     * @var Site
     */
    protected $site;

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

        $this->site = new Site($config);
        $this->registerPlugins();


        // TODO set allowed setters for each plugin

        $this->site->process();

        $elapsed = microtime(true) - $time;
        $style->title('Generated in '.number_format($elapsed, 2).'s');
        return 0;
    }

    private function registerPlugins()
    {
        $plugins = [];

        $finder = new Finder();
        if ($dir = Phar::running(false)) {
            $finder->in(dirname($dir).'/plugins/')
                ->name('*.phar')
                ->depth(' == 0')
            ;
            var_dump(dirname($dir).'/plugins/');
            foreach ($finder as $file) {
                //Include plugin autoloader
                include('phar://'.dirname($dir).'/plugins/'.$file->getRelativePathname().'/vendor/autoload.php');

                // Get plugin class and namespace
                $pluginClass = ucfirst(str_replace('.phar', '', $file->getRelativePathname()));
                $pluginNamespace = '\\Stati\\Plugin\\'.$pluginClass.'\\';

                // Create plugin class
                $pluginFQN = $pluginNamespace.$pluginClass;
                $plugin = new $pluginFQN();

                // Register with event dispatcher
                $this->site->getDispatcher()->addSubscriber($plugin);
                $plugins[] = $plugin;
            }
        } else {
            $dir = __DIR__;
            $finder->in($dir.'/../../plugins/')
                ->directories()
                ->depth(' == 0')
            ;
            var_dump($dir.'/../../plugins/');
            foreach ($finder as $file) {
                //Include plugin autoloader
                include($dir.'/../../plugins/'.$file->getRelativePathname().'/vendor/autoload.php');

                // Get plugin class and namespace
                $pluginClass = ucfirst($file->getRelativePathname());
                $pluginNamespace = '\\Stati\\Plugin\\'.$pluginClass.'\\';

                // Create plugin class
                $pluginFQN = $pluginNamespace.$pluginClass;
                $plugin = new $pluginFQN();

                // Register with event dispatcher
                $this->site->getDispatcher()->addSubscriber($plugin);
                $plugins[] = $plugin;
            }
        }


        return $plugins;
    }

}