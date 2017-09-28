<?php
/**
 * Generator.php
 *
 * Created By: jonathan
 * Date: 22/09/2017
 * Time: 23:02
 */

namespace Stati\Link;

use Symfony\Component\Finder\SplFileInfo;
use Stati\Parser\FrontMatterParser;

class Generator
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getPathForFile(SplFileInfo $file, $type = null)
    {
        $link = isset($this->config['permalink']) ? $this->config['permalink'] : $this->config['site']['permalink'];
        if ($type !== 'post') {
            // This is not a post
            if (!$type) {
                $type = 'html';
            }
            return str_replace('//', '/', '/'.$file->getRelativePath().'/'.pathinfo($file->getBasename(), PATHINFO_FILENAME).'.'.$type);
        }
        $frontMatter = FrontMatterParser::parse($file->getContents());
        if (isset($frontMatter['date'])) {
            $date = new \DateTime();
            $date->setTimestamp(strtotime($frontMatter['date']));
        } else {
            $date = $this->parseDateFromFileName($file->getBasename());
        }
        if ($date) {
            $link = $this->pathFromDate($link, $date);
        }

        $linkConfig = [
            'title' => $this->parseSlugFromFileName($file->getBasename()),
            'slug' => $this->parseSlugFromFileName($file->getBasename()),
            'categories' => array_map('strtolower', isset($frontMatter['categories']) ? $frontMatter['categories'] : [])
        ];

        $link = $this->pathFromFrontMatter($link, $linkConfig);
        return $link;
    }

    public function getUrlForFile(SplFileInfo $file, $type = null)
    {
        $url = $this->config['url'];
        $baseUrl = $this->config['baseurl'];
        return $url.$baseUrl.$this->getPathForFile($file, $type);
    }

    private function parseDateFromFileName($filename)
    {
        try {
            return new \DateTime(substr($filename, 0, 10));
        } catch (\Exception $err) {
//            echo $err->getMessage();
            return false;
        }
    }

    private function parseSlugFromFileName($filename)
    {
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        try {
            $date = new \DateTime(substr($filename, 0, 10));
            return substr($filename, 11);
        } catch (\Exception $err) {
//            echo $err->getMessage();
            return $filename;
        }
    }

    private function pathFromDate($link, \DateTime $date)
    {
        preg_match_all('/(:year|:month|:day|:hour)/', $link, $matches, PREG_PATTERN_ORDER);

        foreach ($matches[1] as $token) {
            $format = '';
            switch ($token) {
                case ':year':
                    $format = 'Y';
                    break;
                case ':month':
                    $format = 'm';
                    break;
                case ':day':
                    $format = 'd';
                    break;
                default:
                    continue;
            }

            $link = str_replace($token, $date->format($format), $link);
        }
        return $link;
    }

    private function pathFromFrontMatter($link, array $frontMatter)
    {
        preg_match_all('/(:title|:categories|:slug)/', $link, $matches, PREG_PATTERN_ORDER);

        foreach ($matches[1] as $token) {
            $replace = '';
            switch ($token) {
                case ':title':
                    $replace = $frontMatter['title'];
                    break;
                case ':categories':
                    $replace = implode('/', $frontMatter['categories']);
                    break;
                case ':slug':
                    $replace = $frontMatter['slug'];
                    break;
                default:
                    continue;
            }

            $link = str_replace($token, $replace, $link);
        }
        return $link;
    }
}
