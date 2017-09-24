<?php

namespace Stati\Command;


use Stati\Renderer\FilesRenderer;
use Stati\Renderer\PostsRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;


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
        $style = new SymfonyStyle($input, $output);
        // Read config file
        $configFile = './_config.yml';
        if (is_file($configFile)) {
            $config = Yaml::parse(file_get_contents($configFile));
        } else {
            $style->error('No config file present. Are you in a jekyll directory ?');
            return;
        }
        // Create _site directory
        if (!is_dir('./_site')) {
            mkdir('./_site');
        }

        $postsRenderer = new PostsRenderer($config);
        $posts = $postsRenderer->render();
        $style->title(count($posts).' post'.(count($posts) > 1 ? 's' : '').' rendered and saved');
        $filesRenderer = new FilesRenderer($config);
        $filesRenderer->render();
    }

}