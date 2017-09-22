<?php

namespace Stati\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Liquid\Template;
use Liquid\Liquid;

class TestCommand extends Command
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

        // Get top level files and parse
        $finder = new Finder();
        $finder->depth(' == 0')
            ->files()
            ->in('./')
//            ->name('/(\.html|contact\.md|\.markdown)$/');
            ->name('/contact\.md$/');

        foreach ($finder as $file) {
            $style->text($file->getRealPath());
            $fileContents = $file->getContents();
            // Remove whitespace at top and bottom of file
            $fileContents = trim($fileContents);
            $split = preg_split("/[\n]*[-]{3}[\n]/", $fileContents, 3, PREG_SPLIT_NO_EMPTY);
            try {
                // Using symfony's YAML parser
                // we can use trim here because we only remove white space
                // at the beginning (first should not have any) and at the end (insignificant)
                $frontMatter = Yaml::parse(trim($split[0]));

            } catch(InvalidArgumentException $e) {
                // This is not YAML
                $style->error($e->getMessage());
            }
            $content = trim($split[1]);
            //If we have a layout
            if (isset($frontMatter) && isset($frontMatter['layout'])) {
                //Use layout as the thing to parse, and pass content as variable.
                $config['content'] = $content;
                $config['page'] = $frontMatter;
                $content = file_get_contents('./_layouts/'.$frontMatter['layout'].'.html');

            }

            Liquid::set('INCLUDE_ALLOW_EXT', true);
            Liquid::set('INCLUDE_PREFIX', './_includes/');
            $template = new Template('./_includes/');
            $template->parse($content);
            echo $template->render($config);

        }



    }
}