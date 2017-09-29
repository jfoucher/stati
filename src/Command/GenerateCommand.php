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
        $timer = microtime(true);
        $this->style->title('Generating site...');

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
        $plugins = ['paginate', 'related'];

        //Use gems for older jekyll config files
        foreach ($this->site->gems as $gem) {
            $plugins[] = str_replace('jekyll-', '', $gem);
        }

        //Use plugins for newer jekyll config files
        if ($this->site->plugins) {
            foreach ($this->site->plugins as $gem) {
                $plugins[] = str_replace('jekyll-', '', $gem);
            }
        }

        $plugins = array_unique($plugins);

        $loadedPlugins = [];

        foreach ($plugins as $requestedPlugin) {
            $pluginExists = false;
            if ($dir = Phar::running(false)) {
                if (is_file(dirname($dir).'/'.$requestedPlugin.'.phar')) {
                    $pluginExists = true;
                    include('phar://'.dirname($dir).'/'.$requestedPlugin.'.phar/vendor/autoload.php');
                }
            } else {
                $dir = __DIR__;
                if (is_file($dir.'/../../'.$requestedPlugin.'/vendor/autoload.php')) {
                    $pluginExists = true;
                    include($dir.'/../../'.$requestedPlugin.'/vendor/autoload.php');
                }
            }

            if ($pluginExists) {
                $pluginClass = ucfirst($requestedPlugin);
                $pluginNamespace = '\\Stati\\Plugin\\'.$pluginClass.'\\';

                // Create plugin class
                $pluginFQN = $pluginNamespace.$pluginClass;
                $loadedPlugin = new $pluginFQN();

                // Register with event dispatcher
                $this->site->getDispatcher()->addSubscriber($loadedPlugin);
                $loadedPlugins[] = $loadedPlugin;
            } else {
                $this->style->error('Sorry, the plugin '.$requestedPlugin.' is not available');
            }

        }

        return $loadedPlugins;
    }
}
