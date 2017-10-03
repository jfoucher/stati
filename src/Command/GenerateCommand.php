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

        $this->site->process();

        $elapsed = microtime(true) - $timer;
        $this->style->title('Generated in '.number_format($elapsed, 2).'s');
        return 0;
    }

    public function consoleOutput(ConsoleOutputEvent $event)
    {
        $method = $event->getMethod();
        $args = $event->getArgs();
        $minLevel = $event->getMinLevel();

        if ($minLevel <= $this->style->getVerbosity()) {
            call_user_func_array([$this->style, $method], $args);
        }
    }

    private function registerPlugins()
    {
        $plugins = [
            'paginate',
            'related',
            'categories',
        ];

        if ($this->site->gems) {
            //Use gems for older jekyll config files
            foreach ($this->site->gems as $gem) {
                $plugins[] = str_replace('jekyll-', '', $gem);
            }
        }

        if ($this->site->plugins) {
            //Use plugins for newer jekyll config files
            if ($this->site->plugins) {
                foreach ($this->site->plugins as $gem) {
                    $plugins[] = str_replace('jekyll-', '', $gem);
                }
            }
        }

        $plugins = array_unique($plugins);

        $loadedPlugins = [];
        $errors = [];

        foreach ($plugins as $requestedPlugin) {
            if ($requestedPlugin === 'seo-tag') {
                $requestedPlugin = 'seo';
            }
            if ($loadedPlugin = $this->loadClass($requestedPlugin)) {
                // Register with event dispatcher
                $this->site->getDispatcher()->addSubscriber($loadedPlugin);
                $loadedPlugins[] = $loadedPlugin;
            } else {
                $errors[] = 'Sorry, the plugin ' . $requestedPlugin . ' is not available';
                continue;
            }
        }

        if (count($errors)) {
            $this->style->error($errors);
        }

        return $loadedPlugins;
    }

    private function loadClass($requestedPlugin)
    {
        $pluginClass = ucfirst($requestedPlugin);
        $pluginNamespace = '\\Stati\\Plugin\\' . $pluginClass . '\\';

        // Create plugin class
        $pluginFQN = $pluginNamespace . $pluginClass;

        if (class_exists($pluginFQN)) {
            $loadedPlugin = new $pluginFQN();
            // Register with event dispatcher
            $this->site->getDispatcher()->addSubscriber($loadedPlugin);
            return $loadedPlugin;
        }

        $this->loadFile($requestedPlugin);
        if (class_exists($pluginFQN)) {
            $loadedPlugin = new $pluginFQN();
            // Register with event dispatcher
            $this->site->getDispatcher()->addSubscriber($loadedPlugin);
            return $loadedPlugin;
        }

        return null;
    }

    private function loadFile($requestedPlugin)
    {
        // Look in site '_plugins' directory first

        if (is_file(getcwd() . '/_plugins/' . $requestedPlugin . '.phar')) {
            var_dump(getcwd());
            include(getcwd() . '/_plugins/' . 'phar://' . $requestedPlugin . '.phar/vendor/autoload.php');
            return;
        }

        if ($dir = Phar::running(false)) {
            if (is_file(dirname($dir) . '/' . $requestedPlugin . '.phar')) {
                include('phar://' . dirname($dir) . '/' . $requestedPlugin . '.phar/vendor/autoload.php');
            }
        } else {
            $dir = __DIR__;
            if (is_file($dir . '/../../' . $requestedPlugin . '/vendor/autoload.php')) {
                include($dir . '/../../' . $requestedPlugin . '/vendor/autoload.php');
            }
        }
    }
}
