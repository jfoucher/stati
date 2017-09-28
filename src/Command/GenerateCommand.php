<?php

namespace Stati\Command;

use Stati\Event\ConsoleOutputEvent;
use Stati\Site\Site;
use Stati\Site\SiteEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;
use Phar;

class GenerateCommand extends Command
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
            ->setName('generate')
            ->setAliases(['g'])
            ->setDescription('Generate static site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->style = new SymfonyStyle($input, $output);
        $this->style->title('Generating site...');
        $timer = microtime(true);
        // Read config file
        $configFile = './_config.yml';

        if (is_file($configFile)) {
            $config = Yaml::parse(file_get_contents($configFile));
        } else {
            $this->style->error('No config file present. Are you in a jekyll directory ?');
            return 1;
        }

        $this->site = new Site($config);
        $this->registerPlugins();

        if ($this->style->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $this->site->getDispatcher()->addListener(SiteEvents::CONSOLE_OUTPUT, array($this, 'consoleOutput'));
        }

        // TODO set allowed setters for each plugin

        $this->site->process();

        $elapsed = microtime(true) - $timer;
        $this->style->title('Generated in '.number_format($elapsed, 2).'s');
        return 0;
    }

    public function consoleOutput(ConsoleOutputEvent $event)
    {
        $method = $event->getMethod();
        $args = $event->getArgs();

        call_user_func_array([$this->style, $method], $args);
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
