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

use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;
use Stati\Event\ConsoleOutputEvent;
use Stati\Site\Site;
use Stati\Site\SiteEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Exception\ParseException;
use Stati\Parser\Yaml;
use Phar;
use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * @var Process
     */
    protected $server;

    protected function configure()
    {
        $this
            ->setName('generate')
            ->setAliases(['g', 'build', 'b'])
            ->setDescription('Generate static site')
            ->addOption('no-cache', 'nc', InputOption::VALUE_NONE)
            ->addOption('watch', 'w', InputOption::VALUE_NONE)
            ->addOption('serve', 's', InputOption::VALUE_NONE)
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port the server should run on', '4000')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->style = new SymfonyStyle($input, $output);

        $config = [];
        // Read config file
        $configFile = './_config.yml';

        if (is_file($configFile)) {
            try {
                $config = Yaml::parse(file_get_contents($configFile));
            } catch (ParseException $err) {
                $this->style->error(['Could not read your site configuration file', $err->getMessage()]);
                return 1;
            }
        }

        if (!$config) {
            $config = [];
        }

        $this->site = new Site($config);

        if ($input->getOption('no-cache')) {
            // delete cache
            $fs = new Filesystem();
            $fs->remove($this->site->getConfig()['cache_dir']);
        }
        $this->registerPlugins();

        if ($this->style->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $this->site->getDispatcher()->addListener(SiteEvents::CONSOLE_OUTPUT, array($this, 'consoleOutput'));
        }

        $timer = microtime(true);
        $this->style->title('Generating site...');
        $this->site->process();
        $elapsed = microtime(true) - $timer;
        $this->style->success('Generated in '.number_format($elapsed, 2).'s');

        $dest = $this->site->getConfig()['destination'];
        if (strpos($dest, './') === 0) {
            $dest = substr($dest, 2);
        }
        $source = $this->site->getConfig()['source'];
        if (strpos($source, './') === 0) {
            $source = substr($source, 2);
        }

        if ($input->getOption('watch')) {
            $files = new \Illuminate\Filesystem\Filesystem();
            $tracker = new Tracker();

            $watcher = new Watcher($tracker, $files);

            $finder = new Finder();
            $finder->in($source.'/')
                ->depth('==0')
                ->notPath('|^'.$dest.'$|')
                ->notName('|^'.$dest.'$|')
            ;

            foreach ($finder as $file) {
                $listener = $watcher->watch($file->getRelativePathname());

                $listener->modify(function ($resource, $path) use ($input, $dest) {
                    $this->style->text('File '.$path.' was modified');
                    $timer = microtime(true);
                    $this->style->text('Regenerating site...');
                    $this->site->process();
                    if ($input->getOption('serve')) {
                        $this->serve($dest, $input->getOption('port'), true, true);
                    }
                    $elapsed = microtime(true) - $timer;
                    $this->style->success('Site regenerated in '.number_format($elapsed, 2).'s');
                });
            }
            if ($input->getOption('serve')) {
                $this->serve($dest, $input->getOption('port'), false, true);
            }
            $this->style->section('Watching files for changes...');
            $watcher->start();
        }

        if ($input->getOption('serve')) {
            $this->serve($dest, $input->getOption('port'));
        }

        return 0;
    }

    public function serve($dir, $port, $regen = false, $watch = false)
    {
        if ($this->server) {
            $this->server->stop();
        }

        if (!$regen) {
            $this->style->success([
                'Stati is now serving your website',
                'Open http://localhost:' . $port . ' to view it',
                'Press Ctrl-C to quit.'
            ]);
        }

        $this->server = new Process('php -S localhost:' . $port, $dir, null, null, null);

        if ($watch) {
            $this->server->start();
        } else {
            $this->server->run();
        }
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
                $errors[] = 'Sorry, the plugin ' . $requestedPlugin . ' could not be loaded';
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
            require_once('phar://' . getcwd() . '/_plugins/' . $requestedPlugin . '.phar/vendor/autoload.php');
            return;
        }


        if ($dir = Phar::running(false)) {
            // if current file a far, look in parent vendor directory (this is in case the phar was installed as a symlink, which is what composer does)
            if (is_file(dirname($dir) . '/../../../bin/' . $requestedPlugin . '.phar')) {
                require_once('phar://' . dirname($dir) . '/../../../bin/' . $requestedPlugin . '.phar/vendor/autoload.php');
            } elseif (is_file(dirname($dir) . '/' . $requestedPlugin . '.phar')) {
                // Or in the same directory as the phar
                require_once('phar://' . dirname($dir) . '/' . $requestedPlugin . '.phar/vendor/autoload.php');
            }
        } else {
            // We're not a phar, look in our own vendor directory
            $dir = __DIR__;
            if (is_file($dir . '/../../vendor/stati/' . $requestedPlugin . '/vendor/autoload.php')) {
                require_once($dir . '/../../vendor/stati/' . $requestedPlugin . '/vendor/autoload.php');
            }
        }
    }
}
