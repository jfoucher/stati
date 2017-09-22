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
use Michelf\MarkdownExtra;
use Stati\Link\Generator;
use Stati\LiquidBlock\Highlight;
use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;
use Stati\Parser\ContentParser;

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
        $finder->depth(' == 1')
            ->files()
            ->in('./')
//            ->name('/(\.html|\.md|\.markdown)$/');
            ->name('/\.md$/');

        // Create _site directory

        if (!is_dir('./_site')) {
            mkdir('./_site');
        }
        $linkGenerator = new Generator($config);
        foreach ($finder as $file) {
            $fileContents = $file->getContents();
            $fileContents = trim($fileContents);

            $rendered = $this->render($fileContents, $style, $config, $file);

            //Put rendered file in _site directory, in it's right place
            $path = $linkGenerator->getPathForFile($file);
            $dir = str_replace('//', '/', './_site/'.pathinfo($path,PATHINFO_DIRNAME));
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents('./_site'.$path, $rendered);
        }



    }


    private function render($content, SymfonyStyle $style, $config, SplFileInfo $file = null)
    {
        var_dump($config);
        Liquid::set('INCLUDE_ALLOW_EXT', true);
        Liquid::set('INCLUDE_PREFIX', './_includes/');
        $linkGenerator = new Generator($config);
        $frontMatter = FrontMatterParser::parse($content);
        $content = ContentParser::parse($content);

        //If we have a layout
        if (isset($frontMatter['layout'])) {
            $newConfig = $config;
            //If file is markdown, parse it first before passing it to liquid
            if ($file && ($file->getExtension() === 'md' || $file->getExtension() === 'mkd' || $file->getExtension() === 'markdown')) {
                $content = MarkdownExtra::defaultTransform($content);
                $newConfig =  [
                    'url' => $linkGenerator->getUrlForFile($file),
                    'site' => $config,
                    'page' => $frontMatter
                ];
            }

            //Parse content with liquid also
            $template = new Template('./_includes/');
            $template->registerTag('highlight', Highlight::class);
            $template->parse($content);
            $rendered = $template->render(array_merge($newConfig, $config));

            //Use layout as the thing to parse, and pass content as variable.
            $newConfig['content'] = $rendered;
            //Pass frontmatter as page variable
            $content = file_get_contents('./_layouts/'.$frontMatter['layout'].'.html');
            return $this->render($content, $style, $newConfig);
        }

        $template = new Template('./_includes/');
        $template->parse($content);
        return $template->render($config);
    }


}